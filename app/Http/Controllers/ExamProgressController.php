<?php

namespace App\Http\Controllers;

use App\Exports\ExamScoreStudentExport;
use App\Models\AssignExamStudent;
use App\Models\ExamClassTransaction;
use App\Models\ManageExam;
use App\Models\MinimalCompletenessCriteria;
use App\Models\RemedialExam;
use App\Models\StudentClass;
use App\Models\StudentClassTransaction;
use App\Models\StudentExamScore;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class ExamProgressController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
   */
  public function index()
  {
    $title = 'Progress Ujian';
    return view('backend.cbt.progress', compact('title'));
  }

  /**
   * Show data in datatable.
   *
   */
  public function datatable()
  {
    $data = ManageExam::with(['subject'])
      ->orderBy('id', 'desc')
      ->where('employee_id', Auth::user()->employee_id)
      ->get();
    return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('class', function ($query) {
        $examClass = ExamClassTransaction::where('exam_id', $query->id)->first();
        $class = StudentClass::where('id', $examClass->class_id)->first();
        if (optional(configuration())->type_school == 1) {
          return $class->class_order;
        } else {
          return $class->gradeLevel->name . '-' . $class->class_order;
        }
      })
      ->addColumn('level', function ($query) {
        if (configuration()->type_school == 1) {
          return $query->semester->number;
        } else {
          return optional($query->gradeLevel)->name;
        }
      })
      ->addColumn('type_exam', function ($query) {
        $type = null;

        if ($query->type_exam == 1) {
          $type = '<span class="badge badge-primary">Ulangan Harian</span>';
        } else if ($query->type_exam == 2) {
          $type = '<span class="badge badge-info">Ujian Tengah Semester</span>';
        } else if ($query->type_exam == 3) {
          $type = '<span class="badge badge-danger">Ujian Akhir Semester</span>';
        } else {
          $type = '<span class="badge badge-success">Try Out</span>';
        }

        return $type;
      })
      ->addColumn('action', function ($query) {
        return '<a href="#" class="btn btn-info btn-sm" title="Progress Ujian" id="' . $query->id . '" onclick="showStudent(' . $query->id . ')"><i class="icon icon-bar-chart"></i></a>';
      })
      ->rawColumns(['name', 'action', 'type_exam'])
      ->make(true);
  }

  /**
   * Show data in datatable student.
   * @param Request $request
   * @return
   * @throws \Exception
   */
  public function datatableStudent(Request $request)
  {
    $examId = $request->exam_id;
    $data = AssignExamStudent::with('student')
      ->where('exam_id', $examId)
      ->orderBy('id', 'asc')
      ->get();
    return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('action', function ($query) use ($examId) {
        return '<a href="#" class="btn btn-success btn-sm" title="Lihat Nilai" id="' . $query->id . '" onclick="showScore(' . $query->student_id . ', ' . $examId . ')"><i class="icon icon-eye"></i></a>';
      })
      ->rawColumns(['action'])
      ->make(true);
  }

  /**
   * Show data in datatable student.
   * @param Request $request
   * @return
   * @throws \Exception
   */
  public function datatableStudentScore(Request $request)
  {
    $studentId = $request->student_id;
    $examId = $request->exam_id;
    $exam = ManageExam::where('id', $examId)->first();
    $minimalCriteria = MinimalCompletenessCriteria::where('subject_id', $exam->subject_id)->first();
    $data = StudentExamScore::with(['assignStudent.student', 'remedial'])
      ->whereHas('assignStudent', function ($query) use ($studentId) {
        $query->where('student_id', $studentId);
      })
      ->whereHas('assignStudent.exam.examClass', function ($query) use ($exam) {
        $query->where('class_id', $exam->examClass->class_id);
      })
      ->orderBy('id', 'desc')
      ->get();
    return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('remedial', function ($query) {
        if (is_null($query->remedial)) {
          return '<b style="font-size: 15px;">-</b>';
        } else {
          return '<b style="font-size: 15px;">Remedial ke-' . $query->remedial->exam_to . '</b>';
        }
      })
      ->addColumn('score', function ($query) {
        return '<b style="font-size: 15px">' . $query->score . '</b>';
      })
      ->addColumn('minimal', function () use ($minimalCriteria) {
        return '<b style="font-size: 15px">' . $minimalCriteria->minimal_criteria . '</b>';
      })
      ->rawColumns(['score', 'minimal', 'remedial'])
      ->make(true);
  }

  /**
   * chart score exam student
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function chartScoreExam(Request $request)
  {
    $examId = $request->exam_id;
    $exam = ManageExam::where('id', $examId)->first();
    $studentClass = StudentClassTransaction::where('class_id', $exam->examClass->class_id)->get();
    $minimalCriteria = MinimalCompletenessCriteria::where('subject_id', $exam->subject_id)->first();
    $assignStudents = AssignExamStudent::where('exam_id', $examId)->get();
    $today = Carbon::now()->format('Y-m-d H:i:s');
    $endDate = Carbon::parse($exam->end_date)->format('Y-m-d H:i:s');

    /* check whether minimal criteria is null or not */
    if (is_null($minimalCriteria)) {
      return response()->json(['status' => 500, 'message' => 'KKM belum ditentukan, silahkan hubungi admin']);
    }

    $data = $this->processCountData($assignStudents, $minimalCriteria, $today, $endDate, $studentClass);
    return response()->json(['status' => 200, 'data' => $data]);
  }

  /**
   * process count data
   * @param $assignStudents
   * @param $minimalCriteria
   * @param $today
   * @param $endDate
   * @param $studentClass
   * @return object
   */
  private function processCountData($assignStudents, $minimalCriteria, $today, $endDate, $studentClass)
  {
    $pass = [];
    $minimal = [];
    $notPass = [];
    $notYetExam = [];
    $notExam = [];

    foreach ($assignStudents as $assignStudent) {
      $scoreData = StudentExamScore::where('assign_student_id', $assignStudent->id)->groupBy('assign_student_id');
      $allData = $scoreData->get();
      if ($allData->isNotEmpty()) {
        $scoreStudents = $scoreData->max('score');
        if ($scoreStudents > $minimalCriteria->minimal_criteria) {
          $pass[] = $scoreStudents;
        } elseif ($scoreStudents == $minimalCriteria->minimal_criteria) {
          $minimal[] = $scoreStudents;
        } else {
          $notPass[] = $scoreStudents;
        }
      } else {
        if ($today < $endDate) {
          $notYetExam[] = 0;
        } else {
          $notExam[] = 0;
        }
      }
    }

    return (object) [
      'total' => count($studentClass),
      'exam' => count($assignStudents),
      'pass' => count($pass),
      'minimal' => count($minimal),
      'not_pass' => count($notPass),
      'not_yet' => count($notYetExam),
      'not_exam' => count($notExam)
    ];
  }

  /**
   * export student score to excel
   * @param $examId
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   */
  public function export($examId)
  {
    $exam = ManageExam::with([
      'examClass.studentClass',
      'examClass.studentClass.gradeLevel',
      'examClass.studentClass.major'
    ])
      ->where('id', $examId)
      ->first();
    $fileName = $this->className($exam);
    return Excel::download(new ExamScoreStudentExport($examId), $fileName . '.xlsx');
  }

  /**
   * class name
   * @param $exam
   * @return string
   */
  private function className($exam)
  {
    $config = optional(configuration())->type_school;
    $name = null;

    if ($config == 1) {
      $name = $exam->examClass->studentClass->subject->name . " Kelas " .
              $exam->examClass->studentClass->class_order;
    } else if ($config == 2) {
      $name = $exam->examClass->studentClass->subject->name . " Kelas " .
              $exam->examClass->studentClass->class_order . " - " .
              $exam->examClass->studentClass->gradeLevel->name . " - " .
              $exam->examClass->studentClass->major->code . " - " .
              $exam->examClass->studentClass->class_order;
    } else {
      $name = $exam->examClass->studentClass->subject->name . " Kelas " .
              $exam->examClass->studentClass->class_order . " - " .
              $exam->examClass->studentClass->gradeLevel->name . " - " .
              $exam->examClass->studentClass->class_order;
    }
    return $name;
  }
}
