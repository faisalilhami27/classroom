<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ManageExam;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index()
  {
    $data = $this->getDashboardData();
    return view('backend.dashboard.index', compact('data'));
  }

  /**
   * get data dashboard
   */
  private function getDashboardData()
  {
    $role = Session::get('role_id');
    $data = null;
    if ($role == 1 || $role == 2) {
      $classes = StudentClass::count();
      $students = Student::count();
    } else {
      $classes = StudentClass::where('employee_id', Auth::user()->employee_id)->count();
      $students = Student::whereHas('studentClassTransaction.studentClass', function ($query) {
        $query->where('employee_id', Auth::user()->employee_id);
      })->count();
    }
    $employees = Employee::count();
    $schoolYear = activeSchoolYear()->early_year . '/' . activeSchoolYear()->end_year;

    $data = (object) [
      'classes' => $classes,
      'students' => $students,
      'employees' => $employees,
      'school_year' => $schoolYear
    ];
    return $data;
  }
}
