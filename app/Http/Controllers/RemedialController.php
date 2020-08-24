<?php

namespace App\Http\Controllers;

use App\Http\Requests\RemedialRequest;
use App\Models\AccommodateExamStudentAnswer;
use App\Models\AssignExamStudent;
use App\Models\ExamClassTransaction;
use App\Models\ManageExam;
use App\Models\MinimalCompletenessCriteria;
use App\Models\QuestionForStudent;
use App\Models\RemedialExam;
use App\Models\StudentClass;
use App\Models\StudentExamScore;
use App\Models\StudentExamViolation;
use App\Models\SupplementaryExam;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class RemedialController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
   */
  public function index()
  {
    $title = 'Daftar Remedial';
    return view('backend.cbt.remedial', compact('title'));
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
        $button = null;

        $button .= '<a href="#" class="btn btn-info btn-sm btn-add" title="Tambah Remedial" id="' . $query->id . '" onclick="addData(' . $query->id . ')"><i class="icon icon-plus"></i></a>
                    <a href="#" class="btn btn-primary btn-sm btn-add-student" title="Lihat Siswa" id="' . $query->id . '" onclick="showStudent(' . $query->id . ')"><i class="icon icon-users"></i></a>';
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
    $data = RemedialExam::with('assignStudent.student')
      ->whereHas('assignStudent', function ($query) use ($examId) {
        $query->where('exam_id', $examId);
      })
      ->orderBy('id', 'asc')
      ->groupBy('assign_student_id')
      ->get();
    return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('violation', function ($query) {
        return '<a href="#" onclick="showViolation('. $query->assign_student_id .', '. $query->id .')">Lihat pelanggaran</a>';
      })
      ->addColumn('action', function ($query) use($examId) {
        return '<a href="#" class="btn btn-success btn-sm btn-score" title="Lihat Nilai" id="' . $query->id . '" onclick="showScore(' . $query->assignStudent->student_id . ', '. $examId .')"><i class="icon icon-eye"></i></a>';
      })
      ->rawColumns(['action', 'violation'])
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
      ->where('remedial_id', '!=', null)
      ->orderBy('id', 'desc')
      ->get();
    return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('exam_to', function ($query) {
        return '<b style="font-size: 15px"> Remedial ke-' . $query->remedial->exam_to . '</b>';
      })
      ->addColumn('score', function ($query) {
        return '<b style="font-size: 15px">' . $query->score . '</b>';
      })
      ->addColumn('minimal', function () use ($minimalCriteria) {
        return '<b style="font-size: 15px">' . $minimalCriteria->minimal_criteria . '</b>';
      })
      ->rawColumns(['score', 'minimal', 'exam_to'])
      ->make(true);
  }

  /**
   * get exam by class
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getRemedialByClass(Request $request)
  {
    $subjectName = subjectName();
    $level = level();
    $classId = $request->class_id;
    $remedial = RemedialExam::with([
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
      'data' => $remedial,
      'subject_name' => $subjectName,
      'level' => $level
    ]);
  }

  /**
   * show violation
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function showViolation(Request $request)
  {
    $assignStudentId = $request->id;
    $remedialId = $request->remedial_id;
    $violations = StudentExamViolation::where('assign_student_id', $assignStudentId)
      ->where('remedial_id', $remedialId)
      ->get();
    return response()->json(['status' => 200, 'data' => $violations]);
  }

  /**
   * get score student remedial
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getScoreStudentRemedial(Request $request)
  {
    $examId = $request->exam_id;
    $remedialId = $request->remedial_id;
    $studentId = Auth::user()->student_id;
    $exam = ManageExam::where('id', $examId)->first();
    $minimumCriteria = MinimalCompletenessCriteria::where('subject_id', $exam->subject_id)->first();
    $assignStudent = AssignExamStudent::where('exam_id', $examId)
      ->where('student_id', $studentId)
      ->first();
    $remedial = RemedialExam::where('assign_student_id', $assignStudent->id)
      ->where('id', $remedialId)
      ->first();
    $scoreStudentRemedial = $remedial->scoreStudent->score;
    return response()->json([
      'status' => 200,
      'score' => $scoreStudentRemedial,
      'minimal' => optional($minimumCriteria)->minimal_criteria
    ]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param RemedialRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(RemedialRequest $request)
  {
    $selectQuestion = $request->select_question;
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
    $data = [];

    if (!is_null($checkDateMessage)) {
      return response()->json(['status' => 500, 'message' => $checkDateMessage]);
    }

    /* insert to table remedial */
    foreach ($students as $student) {
      $remedial = RemedialExam::where('assign_student_id', $student)
        ->orderBy('id', 'desc')
        ->first();
      $data[] = [
        'assign_student_id' => $student,
        'start_date' => $startDate . ' ' . $startTime,
        'end_date' => $endDate . ' ' . $endTime,
        'status' => 0,
        'exam_to' => (is_null($remedial)) ? 1 : ($remedial->exam_to + 1),
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ];
    }
    $insert = RemedialExam::insert($data);

    /* check whether using previous question or new question */
    if ($selectQuestion == 2) {
      DB::transaction(function () use ($students, $data, $exam) {
        QuestionForStudent::whereIn('assign_student_id', $students)->delete();
        $this->storeNewQuestion($students, $exam);
        $this->deleteOldStudentAnswer($students, $exam->id);

        try {
          DB::commit();
          return true;
        } catch (\Exception $e) {
          DB::rollBack();
          return response()->json(['status' => 500, 'message' => 'Terjadi kesalahan pada server']);
        }
      }, 3);
    } else {
      $this->deleteOldStudentAnswer($students, $examId);
    }

    if ($insert) {
      $json = ['status' => 200, 'message' => 'Remedial berhasil ditambahkan'];
    } else {
      $json = ['status' => 500, 'message' => 'Remedial gagal ditambahkan'];
    }
    return response()->json($json);
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
   * delete old student answer
   * @param $student
   * @param $examId
   */
  private function deleteOldStudentAnswer($student, $examId)
  {
    $assignStudents = AssignExamStudent::whereIn('id', $student)->get();
    foreach ($assignStudents as $assignStudent) {
      AccommodateExamStudentAnswer::where('exam_id', $examId)
        ->where('student_id', $assignStudent->student_id)
        ->delete();
    }
  }

  /**
   * store new question
   * @param $students
   * @param $exam
   */
  private function storeNewQuestion($students, $exam)
  {
    $arrayQuestion = [];
    foreach ($students as $student) {
      $remedial = RemedialExam::where('assign_student_id', $student)->first();
      $questionExams = $exam->question()->inRandomOrder()->limit($exam->amount_question)->get();
      foreach ($questionExams as $question) {
        $arrayQuestion[] = [
          'exam_question_id' => $question->id,
          'assign_student_id' => $student,
          'remedial_exam_id' => $remedial->id,
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
   * Show the form for editing the specified resource.
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getStudent(Request $request)
  {
    $examId = $request->exam_id;
    $category = $request->select_student_category;
    $exam = ManageExam::where('id', $examId)->first();
    $today = Carbon::now()->format('Y-m-d H:i:s');
    $assignStudents = AssignExamStudent::where('exam_id', $examId)->where('status_generate', 1)->get();
    $checkData = $this->checkingData($exam, $assignStudents);
    $minimalCriteria = MinimalCompletenessCriteria::where('subject_id', $exam->subject_id)
      ->where('school_year_id', activeSchoolYear()->id)
      ->first();
    $arrayStudents = [];

    /* checking data */
    if (!is_null($checkData)) {
      return response()->json(['status' => 500, 'message' => $checkData]);
    }

    foreach ($assignStudents as $assignStudent) {
      $remedial = RemedialExam::where('assign_student_id', $assignStudent->id)->orderBy('id', 'desc')->first();
      $supplementary = SupplementaryExam::where('assign_student_id', $assignStudent->id)->first();
      if (is_null($remedial)) {
        $studentScore = $this->checkingScore($category, $assignStudent, $minimalCriteria, null, $supplementary);
        if (!is_null($studentScore)) {
          if ($studentScore->score <= $minimalCriteria->minimal_criteria) {
            $arrayStudents[] = $assignStudent->student_id;
          }
        }
      } else {
        $studentScore = $this->checkingScore($category, $assignStudent, $minimalCriteria, $remedial->id);
        if (!is_null($studentScore)) {
          $endDate = Carbon::parse($remedial->end_date)->format('Y-m-d H:i:s');
          if ($endDate >= $today) {
            if ($studentScore->score <= $minimalCriteria->minimal_criteria) {
              $arrayStudents[] = $assignStudent->student_id;
            }
          }
        }
      }
    }

    if (count($arrayStudents) > 0) {
      $data = AssignExamStudent::with('student')
        ->where('exam_id', $examId)
        ->whereIn('student_id', $arrayStudents)
        ->get();
      return response()->json(['status' => 200, 'data' => $data]);
    } else {
      return response()->json(['status' => 500, 'message' => 'Tidak ada siswa yang dapat melakukan remedial']);
    }
  }

  /**
   * checking score
   * @param $category
   * @param $assignStudent
   * @param $minimalCriteria
   * @param null $remedialId
   * @param null $supplementary
   * @return
   */
  private function checkingScore($category, $assignStudent, $minimalCriteria, $remedialId = null, $supplementary = null)
  {
    $studentScore = StudentExamScore::where('assign_student_id', $assignStudent->id)
      ->orderBy('id', 'desc')
      ->where('remedial_id', $remedialId);

    if ($category == 1) {
      $studentScore->where('score', '<', $minimalCriteria->minimal_criteria);
    } else if ($category == 2) {
      $studentScore->where('score', '=', $minimalCriteria->minimal_criteria);
    } else {
      $studentScore->where('score', '<=', $minimalCriteria->minimal_criteria);
    }

    (!is_null($supplementary)) ?: $studentScore->where('supplementary_id', optional($supplementary)->id);
    return $studentScore
      ->orderBy('id', 'desc')
      ->first();
  }

  /**
   * checking data
   * @param $exam
   * @param $checkStudents
   * @return string
   */
  private function checkingData($exam, $checkStudents)
  {
    $message = null;
    if ($exam->status != 1) {
      $message = 'Ujian belum diaktifkan';
    }

    /* check if exam not yet started */
    if ($checkStudents->isEmpty()) {
      $message = 'Tidak ada siswa yang diajukan ujian';
    }

    return $message;
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
