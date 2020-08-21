<?php

namespace App\Http\Controllers;

use App\Models\ManageExam;
use App\Models\StudentClass;
use App\Models\StudentTask;
use App\Models\Task;
use App\Models\UserEmployee;
use App\Models\UserStudent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Contracts\Support\Renderable
   */
  public function index()
  {
    $data = $this->userData();
    $encode = collect($data);
    return view('frontend.index', compact('encode'));
  }

  /**
   * page by class
   * @param Request $request
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function classPage(Request $request)
  {
    $segment = $request->segment(2);
    $user = Auth::user();
    $encode = collect($this->userData());

    if (Auth::guard('employee')->check()) {
      $class = StudentClass::where('id', $segment)
        ->where('employee_id', $user->employee_id)
        ->first();
    } else {
      $class = StudentClass::where('id', $segment)
        ->whereHas('classTransaction', function ($query) use ($user) {
          $query->where('student_id', $user->student_id);
        })
        ->first();
    }

    if (is_null($class) || empty($class)) {
      return view('errors.error_class');
    }
    return view('frontend.index', compact('encode'));
  }

  /**
   * page by task
   * @param Request $request
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function taskPage(Request $request)
  {
    $segment = $request->segment(3);
    $user = Auth::user();
    $encode = collect($this->userData());

    if (Auth::guard('employee')->check()) {
      $task = Task::where('id', $segment)
        ->where('employee_id', $user->employee_id)
        ->first();
    } else {
      $task = StudentTask::where('task_id', $segment)
        ->where('student_id', $user->student_id)
        ->first();
    }

    if (is_null($task) || empty($task)) {
      return view('errors.error_task');
    }
    return view('frontend.index', compact('encode'));
  }

  /**
   * exam page
   * @param $id
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function examPage($id)
  {
    $encode = collect($this->userData());
    $today = Carbon::now()->format('Y-m-d H:i:s');
    $exam = ManageExam::where('id', $id)
      ->whereHas('singleAssignStudent', function ($query) {
        $query->where('student_id', Auth::user()->student_id);
      })
      ->first();

    if (is_null($exam) || empty($exam)) {
      return view('errors.error_exam');
    }
    return view('frontend.index', compact('encode'));
  }

  /**
   * user data
   * @return object
   */
  private function userData()
  {
    if (Auth::guard('student')->check()) {
      $user = UserStudent::with('student')->where('student_id', Auth::user()->student_id)->first();
      $data = (object)[
        'id' => $user->id,
        'username' => $user->username,
        'user_id' => $user->student_id,
        'name' => $user->student->name,
        'email' => $user->student->email,
        'phone' => $user->student->phone_number,
        'identity_number' => $user->student->student_identity_number,
        'photo' => $user->student->photo,
        'color' => $user->student->color,
        'guard' => 'student'
      ];
    } else {
      $user = UserEmployee::with('employee')->where('employee_id', Auth::user()->employee_id)->first();

      $data = (object)[
        'id' => $user->id,
        'username' => $user->username,
        'user_id' => $user->employee_id,
        'name' => $user->employee->name,
        'email' => $user->employee->email,
        'phone' => $user->employee->phone_number,
        'identity_number' => $user->employee->employee_identity_number,
        'photo' => $user->employee->photo,
        'color' => $user->employee->color,
        'guard' => 'employee'
      ];
    }
    return $data;
  }
}
