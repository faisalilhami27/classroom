<?php

namespace App\Http\Controllers;

use App\Events\AddNewComment;
use App\Models\Employee;
use App\Models\ForumComment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumCommentController extends Controller
{
  /**
   * get data comment
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getDataComment(Request $request)
  {
    $postingId = $request->posting_id;
    $comments = ForumComment::where('posting_id', $postingId)->get();
    $data = [];

    foreach ($comments as $comment) {
      if ($comment->user_type == 'employee') {
        $data[] = [
          'message' => $comment->message,
          'date' => $comment->created_at->diffForHumans(),
          'user' => [
            'id' => optional($comment->employee)->id,
            'name' => optional($comment->employee)->name . ' (Guru)',
            'photo' => (is_null(optional($comment->employee)->photo)) ? null : asset('storage/' . optional($comment->employee)->photo),
            'color' => $comment->employee->color
          ]
        ];
      } else {
        $data[] = [
          'message' => $comment->message,
          'date' => $comment->created_at->diffForHumans(),
          'user' => [
            'id' => optional($comment->student)->id,
            'name' => optional($comment->student)->name,
            'photo' => (is_null(optional($comment->student)->photo)) ? null : asset('storage/' . optional($comment->student)->photo),
            'color' => $comment->student->color
          ]
        ];
      }
    }

    return response()->json(['status' => 200, 'data' => $data]);
  }

  /**
   * add comment from user
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function addComment(Request $request)
  {
    $message = $request->message;
    $postingId = $request->posting_id;

    /* check whether user is employee or student */
    if (Auth::guard('employee')->check()) {
      $userId = Auth::user()->employee_id;
      $user = Employee::where('id', $userId)->first();
      $userType = 'employee';
    } else {
      $userId = Auth::user()->student_id;
      $user = Student::where('id', $userId)->first();
      $userType = 'student';
    }

    /* save data comment */
    $comment = $user->comment()->create([
      'message' => $message,
      'posting_id' => $postingId,
      'user_id' => $userId,
      'user_type' => $userType
    ]);

    $data = [
      'message' => $message,
      'date' => $comment->created_at->diffForHumans(),
      'user' => [
        'id' => $user->id,
        'name' => (Auth::guard('employee')->check()) ? $user->name . ' (Guru)' : $user->name,
        'photo' => (is_null($user->photo)) ? null : asset('storage/' . $user->photo)
      ]
    ];

    /* trigger to broadcast */
    event(new AddNewComment($comment, $postingId));
    return response()->json(['status' => 200, 'data' => $data]);
  }
}
