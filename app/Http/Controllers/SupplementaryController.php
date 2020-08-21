<?php

namespace App\Http\Controllers;

use App\Models\AssignExamStudent;
use App\Models\ExamClassTransaction;
use App\Models\ManageExam;
use App\Models\MinimalCompletenessCriteria;
use App\Models\QuestionForStudent;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\StudentExamScore;
use App\Models\SupplementaryExam;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class SupplementaryController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
   */
  public function index()
  {
    $title = 'Daftar Ujian Susulan';
    return view('backend.cbt.supplementary', compact('title'));
  }

  /**
   * Show data in datatable.
   *
   */
  public function datatable()
  {
    $data = ManageExam::with(['subject', 'semester', 'gradeLevel'])
      ->orderBy('id', 'desc')
      ->where('employee_id', Auth::user()->employee_id)
      ->get();
    return DataTables::of($data)
      ->addIndexColumn()
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
      ->addColumn('class', function ($query) {
        $examClass = ExamClassTransaction::where('exam_id', $query->id)->first();
        $class = StudentClass::where('id', $examClass->class_id)->first();
        if (optional(configuration())->type_school == 1) {
          return $class->class_order;
        } else {
          if (optional(configuration())->type_school == 2) {
            return $class->gradeLevel->name . ' - ' . $class->major->code . ' - ' . $class->class_order;
          } else {
            return $class->gradeLevel->name . ' - ' . $class->class_order;
          }
        }
      })
      ->addColumn('action', function ($query) {
        $button = null;
        $endDate = Carbon::parse($query->end_date)->format('Y-m-d H:i:s');
        $today = Carbon::now()->format('Y-m-d H:i:s');

        if ($today <= $endDate) {
          $button = '<a href="#" class="btn btn-info btn-sm" onclick="notYetStarted()"><i class="icon icon-plus"></i></a>';
        } else {
          $button = '<a href="#" class="btn btn-info btn-sm btn-add" title="Tambah Ujian Susulan" id="' . $query->id . '" onclick="addData(' . $query->id . ')"><i class="icon icon-plus"></i></a>
                     <a href="#" class="btn btn-primary btn-sm btn-add-student" title="Lihat Siswa" id="' . $query->id . '" onclick="showStudent(' . $query->id . ')"><i class="icon icon-users"></i></a>';
        }
        return $button;
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
    $data = SupplementaryExam::with('assignStudent.student')
      ->whereHas('assignStudent', function ($query) use ($examId) {
        $query->where('exam_id', $examId);
      })
      ->orderBy('id', 'asc')
      ->groupBy('assign_student_id')
      ->get();
    return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('action', function ($query) use ($examId) {
        return '<a href="#" class="btn btn-success btn-sm btn-score" title="Lihat Nilai" id="' . $query->id . '" onclick="showScore(' . $query->assignStudent->student_id . ', ' . $examId . ')"><i class="icon icon-eye"></i></a>';
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
    $data = StudentExamScore::with(['assignStudent.student', 'supplementary'])
      ->whereHas('assignStudent', function ($query) use ($studentId) {
        $query->where('student_id', $studentId);
      })
      ->where('supplementary_id', '!=', null)
      ->orderBy('id', 'desc')
      ->get();
    return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('score', function ($query) {
        return '<b style="font-size: 15px">' . $query->score . '</b>';
      })
      ->addColumn('minimal', function () use ($minimalCriteria) {
        return '<b style="font-size: 15px">' . $minimalCriteria->minimal_criteria . '</b>';
      })
      ->rawColumns(['score', 'minimal'])
      ->make(true);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getStudent(Request $request)
  {
    $examId = $request->exam_id;
    $exam = ManageExam::where('id', $examId)->first();
    $students = Student::whereHas('studentClassTransaction', function ($query) use ($exam) {
      $query->where('class_id', $exam->examClass->class_id);
    })
      ->get();
    $data = [];

    foreach ($students as $student) {
      $assignStudent = $this->checkStudent($examId, $student->id);
      if (is_null($assignStudent)) {
        $data[] = $student;
      }
    }

    if (count($data) > 0) {
      return response()->json(['status' => 200, 'data' => $data]);
    } else {
      return response()->json(['status' => 500, 'message' => 'Tidak ada siswa yang dapat melakukan ujian susulan']);
    }
  }

  /**
   * check student
   * @param $examId
   * @param $studentId
   * @return
   */
  private function checkStudent($examId, $studentId)
  {
    return AssignExamStudent::where('exam_id', $examId)
      ->where('student_id', $studentId)
      ->first();
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $startDate = $request->start_date;
    $endDate = $request->end_date;
    $startTime = $request->start_time;
    $endTime = $request->end_time;
    $students = $request->student_id;
    $examId = $request->id;
    $exam = ManageExam::where('id', $examId)->first();
    $today = Carbon::now()->format('Y-m-d H:i');
    $formatStartDate = $startDate . ' ' . $startTime;
    $formatEndDate = $endDate . ' ' . $endTime;
    $checkDateMessage = $this->checkDate($today, $formatStartDate, $formatEndDate);
    $assignStudents = $this->insertAssignStudent($students, $exam->id);

    if (!is_null($checkDateMessage)) {
      return response()->json(['status' => 500, 'message' => $checkDateMessage]);
    }

    $insert = DB::transaction(function () use ($assignStudents, $formatStartDate, $formatEndDate, $exam) {
      $this->insertSupplementaryExam($assignStudents, $formatStartDate, $formatEndDate);
      $this->insertNewQuestion($assignStudents, $exam);

      try {
        DB::commit();
        return true;
      } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['status' => 500, 'message' => 'Terjadi kesalahan pada server']);
      }
    }, 3);

    if ($insert) {
      $json = ['status' => 200, 'message' => 'Ujian susulan berhasil ditambahkan'];
    } else {
      $json = ['status' => 500, 'message' => 'Ujian susulan gagal ditambahkan'];
    }
    return response()->json($json);
  }

  /**
   * insert new question
   * @param $students
   * @param $exam
   */
  private function insertNewQuestion($students, $exam)
  {
    $arrayQuestion = [];
    foreach ($students as $student) {
      $supplementaryExam = SupplementaryExam::where('assign_student_id', $student)->first();
      $questionExams = $exam->question()->inRandomOrder()->limit($exam->amount_question)->get();
      foreach ($questionExams as $question) {
        $arrayQuestion[] = [
          'exam_question_id' => $question->id,
          'assign_student_id' => $student,
          'supplementary_exam_id' => $supplementaryExam->id,
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ];
      }
    }

    if (!empty($arrayQuestion)) {
      QuestionForStudent::insert($arrayQuestion);
    }
  }

  /**
   * insert supplementary exam
   * @param $assignStudents
   * @param $startDate
   * @param $endDate
   * @return void
   */
  private function insertSupplementaryExam($assignStudents, $startDate, $endDate)
  {
    $arraySupplementaryExam = [];
    foreach ($assignStudents as $assignStudent) {
      $arraySupplementaryExam[] = [
        'assign_student_id' => $assignStudent,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'status' => 0,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ];
    }
    SupplementaryExam::insert($arraySupplementaryExam);
  }

  /**
   * store assign student
   * @param $students
   * @param $examId
   * @return array
   */
  private function insertAssignStudent($students, $examId)
  {
    $arrayAssignStudent = [];
    foreach ($students as $student) {
      $assignStudent = AssignExamStudent::create([
        'exam_id' => $examId,
        'student_id' => $student,
        'status' => 0,
        'status_generate' => 1,
        'violation' => 0
      ]);
      array_push($arrayAssignStudent, $assignStudent->id);
    }
    return $arrayAssignStudent;
  }

  /**
   * check date
   * @param $today
   * @param $formatStartDate
   * @param $formatEndDate
   * @return string
   */
  private function checkDate($today, $formatStartDate, $formatEndDate)
  {
    $message = null;
    if ($today > $formatStartDate || $today > $formatEndDate) {
      $message = "Tanggal dan Waktu harus lebih dari waktu sekarang";
    }

    /* check whether end date is smaller than start date */
    if ($formatStartDate >= $formatEndDate) {
      $message = "Tanggal dan Waktu Selesai minimal harus lebih dari tanggal dan waktu mulai";
    }
    return $message;
  }

  /**
   * get exam by class
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getSupplementaryExamByClass(Request $request)
  {
    $subjectName = subjectName();
    $level = level();
    $classId = $request->class_id;
    $supplementary = SupplementaryExam::with([
      'assignStudent.exam.subject',
      'assignStudent.exam.semester',
      'assignStudent.exam.gradeLevel',
      'answer'
    ])
      ->whereHas('assignStudent', function ($query) {
        $query->where('student_id', Auth::user()->student_id);
      })
      ->whereHas('assignStudent.exam.examClass', function ($query) use ($classId) {
        $query->where('class_id', $classId);
      })
      ->paginate(5);

    return response()->json([
      'status' => 200,
      'data' => $supplementary,
      'subject_name' => $subjectName,
      'level' => $level
    ]);
  }

  /**
   * get score student supplementary exam
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getScoreStudentSupplementaryExam(Request $request)
  {
    $examId = $request->exam_id;
    $supplementaryId = $request->supplementary_id;
    $studentId = Auth::user()->student_id;
    $exam = ManageExam::where('id', $examId)->first();
    $minimumCriteria = MinimalCompletenessCriteria::where('subject_id', $exam->subject_id)->first();
    $assignStudent = AssignExamStudent::where('exam_id', $examId)
      ->where('student_id', $studentId)
      ->first();
    $supplementary = SupplementaryExam::where('assign_student_id', $assignStudent->id)
      ->where('id', $supplementaryId)
      ->first();
    $scoreStudentRemedial = $supplementary->scoreStudent->score;
    return response()->json([
      'status' => 200,
      'score' => $scoreStudentRemedial,
      'minimal' => optional($minimumCriteria)->minimal_criteria
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param int $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @param int $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    //
  }
}
