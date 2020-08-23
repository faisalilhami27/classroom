<?php

namespace App\Http\Controllers;

use App\Events\AnswerDiscussionEvent;
use App\Events\DeleteAnswerDiscussionEvent;
use App\Events\DeleteDiscussionEvent;
use App\Events\DiscussionEvent;
use App\Models\AnswerDiscussion;
use App\Models\Discussion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
  /**
   * make discussion
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function makeDiscussion(Request $request)
  {
    $message = htmlspecialchars($request->message);
    $materialId = $request->material_id;
    $classId = $request->class_id;
    $studentId = Auth::user()->student_id;
    $employeeId = Auth::user()->employee_id;
    $discussion = Discussion::create([
      'message' => $message,
      'material_id' => $materialId,
      'class_id' => $classId,
      'student_id' => (is_null($studentId)) ? null : $studentId,
      'employee_id' => (is_null($employeeId)) ? null : $employeeId,
    ]);
    $getDiscussion = $this->getDiscussion($discussion->id);
    $countDiscussion = $this->countDiscussion($materialId, $classId);
    event(new DiscussionEvent($getDiscussion, $classId, $countDiscussion, 'create'));
    if ($discussion) {
      return response()->json([
        'status' => 200,
        'discussion' => $getDiscussion,
        'count' => $countDiscussion
      ]);
    } else {
      return response()->json([
        'status' => 500,
        'message' => 'Terjadi kesalahan pada server',
      ]);
    }
  }

  /**
   * update discussion
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function updateDiscussion(Request $request)
  {
    $message = htmlspecialchars($request->message);
    $discussionId = $request->discussion_id;
    $discussion = Discussion::where('id', $discussionId)->first();
    $discussion->update(['message' => $message]);
    $getDiscussion = $this->getDiscussion($discussion->id);
    $countDiscussion = $this->countDiscussion($discussion->material_id, $discussion->class_id);
    event(new DiscussionEvent($getDiscussion, $discussion->class_id, $countDiscussion, 'update'));
    if ($discussion) {
      return response()->json([
        'status' => 200,
        'discussion' => $getDiscussion,
        'count' => $countDiscussion
      ]);
    } else {
      return response()->json([
        'status' => 500,
        'message' => 'Terjadi kesalahan pada server',
      ]);
    }
  }

  /**
   * update answer discussion
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function updateAnswerDiscussion(Request $request)
  {
    $message = htmlspecialchars($request->message);
    $answerId = $request->answer_id;
    $answer = AnswerDiscussion::with(['student', 'employee'])
      ->where('id', $answerId)
      ->first();
    $answer->update(['message' => $message]);
    $countDiscussion = $this->countAnswerDiscussion($answerId);
    event(new AnswerDiscussionEvent($answer, 1, $countDiscussion, $answer->discussion_id, 'update'));
    if ($answer) {
      return response()->json([
        'status' => 200,
        'answer' => $answer,
        'count' => $countDiscussion
      ]);
    } else {
      return response()->json([
        'status' => 500,
        'message' => 'Terjadi kesalahan pada server',
      ]);
    }
  }

  /**
   * count discussion
   * @param $materialId
   * @param $classId
   * @return
   */
  private function countDiscussion($materialId, $classId)
  {
    return Discussion::where('material_id', $materialId)
      ->where('class_id', $classId)
      ->count();
  }

  /**
   * count answer discussion
   * @param $answerId
   * @return
   */
  private function countAnswerDiscussion($answerId)
  {
    return AnswerDiscussion::where('id', $answerId)->count();
  }

  /**
   * answer discussion
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function answerDiscussion(Request $request)
  {
    $message = htmlspecialchars($request->message);
    $discussionId = $request->discussion_id;
    $discussion = Discussion::where('id', $discussionId)->first();
    $studentId = Auth::user()->student_id;
    $employeeId = Auth::user()->employee_id;

    /* check discussion */
    if (is_null($discussion)) {
      return response()->json(['status' => 500, 'message' => 'Diskusi ini sudah tidak tersedia atau sudah dihapus']);
    }

    $answer = AnswerDiscussion::create([
      'message' => $message,
      'discussion_id' => $discussionId,
      'student_id' => (is_null($studentId)) ? null : $studentId,
      'employee_id' => (is_null($employeeId)) ? null : $employeeId,
    ]);
    $getAnswerDiscussion = $this->getAnswerDiscussion($answer->id);
    $countAnswerDiscussion = AnswerDiscussion::where('discussion_id', $discussionId)->count();
    event(new AnswerDiscussionEvent($getAnswerDiscussion, $discussion->class_id, $countAnswerDiscussion, $discussionId, 'create'));
    if ($answer) {
      return response()->json([
        'status' => 200,
        'answer' => $getAnswerDiscussion,
        'count' => $countAnswerDiscussion
      ]);
    } else {
      return response()->json([
        'status' => 500,
        'message' => 'Terjadi kesalahan pada server',
      ]);
    }
  }

  /**
   * get discussion
   * @param $id
   * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
   */
  private function getDiscussion($id)
  {
    return Discussion::with(['student', 'employee'])
      ->where('id', $id)
      ->orderBy('id', 'asc')
      ->first();
  }

  /**
   * get discussion
   * @param $id
   * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
   */
  private function getAnswerDiscussion($id)
  {
    return AnswerDiscussion::with(['student', 'employee'])
      ->where('id', $id)
      ->orderBy('id', 'asc')
      ->first();
  }

  /**
   * load more discussion
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function loadDiscussion(Request $request)
  {
    $materialId = $request->material_id;
    $classId = $request->class_id;
    $discussion = Discussion::with([
      'student',
      'employee',
      'answer.student',
      'answer.employee',
    ])
      ->where('material_id', $materialId)
      ->where('class_id', $classId)
      ->orderBy('id', 'asc')
      ->get();
    return response()->json(['status' => 200, 'discussion' => $discussion]);
  }

  /**
   * load more answer discussion
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function loadAnswerDiscussion(Request $request)
  {
    $discussionId = $request->discussion_id;
    $discussion = AnswerDiscussion::with([
      'student',
      'employee',
    ])
      ->where('discussion_id', $discussionId)
      ->orderBy('id', 'asc')
      ->get();
    return response()->json(['status' => 200, 'answer' => $discussion]);
  }

  /**
   * delete discussion
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroyDiscussion(Request $request)
  {
    $discussionId = $request->discussion_id;
    $classId = $request->class_id;
    $discussion = Discussion::where('id', $discussionId)->first();
    $discussion->answer()->delete();
    $discussion->delete();
    event(new DeleteDiscussionEvent($classId, $discussionId));
    if ($discussion) {
      return response()->json(['status' => 200]);
    } else {
      return response()->json(['status' => 500, 'message' => 'Terjadi kesalahan di server']);
    }
  }

  /**
   * delete discussion
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroyAnswerDiscussion(Request $request)
  {
    $answerId = $request->answer_id;
    $classId = $request->class_id;
    $discussionId = $request->discussion_id;
    $answer = AnswerDiscussion::where('id', $answerId)->first();
    $answer->delete();
    event(new DeleteAnswerDiscussionEvent($classId, $discussionId, $answerId));
    if ($answer) {
      return response()->json(['status' => 200]);
    } else {
      return response()->json(['status' => 500, 'message' => 'Terjadi kesalahan di server']);
    }
  }
}
