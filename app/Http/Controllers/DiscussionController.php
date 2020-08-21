<?php

namespace App\Http\Controllers;

use App\Events\AnswerDiscussionEvent;
use App\Events\DiscussionEvent;
use App\Models\AnswerDiscussion;
use App\Models\Discussion;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
  /**
   * make discussion
   * @param Request $request
   * @param Material $material
   * @return \Illuminate\Http\JsonResponse
   */
  public function makeDiscussion(Request $request)
  {
    $message = $request->message;
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
    event(new DiscussionEvent($getDiscussion, $classId));
    return response()->json(['status' => 200, 'discussion' => $getDiscussion]);
  }

  /**
   * answer discussion
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function answerDiscussion(Request $request)
  {
    $message = $request->message;
    $discussionId = $request->discussion_id;
    $discussion = Discussion::where('id', $discussionId)->first();
    $studentId = Auth::user()->student_id;
    $employeeId = Auth::user()->employee_id;
    AnswerDiscussion::create([
      'message' => $message,
      'discussion_id' => $discussionId,
      'student_id' => (is_null($studentId)) ? null : $studentId,
      'employee_id' => (is_null($employeeId)) ? null : $employeeId,
    ]);
    $getDiscussion = $this->getAnswerDiscussion($discussionId);
    $countAnswerDiscussion = AnswerDiscussion::where('discussion_id', $discussionId)->count();
    event(new AnswerDiscussionEvent($getDiscussion, $discussion->class_id, $countAnswerDiscussion, $discussionId));
    return response()->json(['status' => 200, 'answer' => $getDiscussion, 'count' => $countAnswerDiscussion]);
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
      ->orderBy('id', 'desc')
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
      ->where('discussion_id', $id)
      ->orderBy('id', 'desc')
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
      'employee'
    ])
      ->where('material_id', $materialId)
      ->where('class_id', $classId)
      ->orderBy('id', 'desc')
      ->paginate(3);
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
      ->orderBy('id', 'desc')
      ->paginate(3);
    return response()->json(['status' => 200, 'discussion' => $discussion]);
  }
}
