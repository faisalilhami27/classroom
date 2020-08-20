<?php

namespace App\Http\Controllers;

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
    return response()->json(['status' => 200, 'discussion' => $discussion]);
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
    $studentId = Auth::user()->student_id;
    $employeeId = Auth::user()->employee_id;
    $answer = AnswerDiscussion::create([
      'message' => $message,
      'discussion_id' => $discussionId,
      'student_id' => (is_null($studentId)) ? null : $studentId,
      'employee_id' => (is_null($employeeId)) ? null : $employeeId,
    ]);
    return response()->json(['status' => 200, 'answer' => $answer]);
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
      'answer',
      'answer.student',
      'answer.employee',
    ])
      ->where('material_id', $materialId)
      ->where('class_id', $classId)
      ->orderBy('id', 'desc')
      ->paginate(3);
    return response()->json(['status' => 200, 'discussion' => $discussion]);
  }
}
