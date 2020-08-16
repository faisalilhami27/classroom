<?php

namespace App\Http\Controllers;

use App\Http\Requests\ManageExamRequest;
use App\Models\AccommodateExamQuestion;
use App\Models\AnswerKey;
use App\Models\AssignExamStudent;
use App\Models\ExamClassTransaction;
use App\Models\ExamRules;
use App\Models\GradeLevel;
use App\Models\Major;
use App\Models\ManageExam;
use App\Models\MinimalCompletenessCriteria;
use App\Models\QuestionBank;
use App\Models\QuestionForStudent;
use App\Models\SchoolYear;
use App\Models\Semester;
use App\Models\StudentClass;
use App\Models\StudentExamScore;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class ManageExamController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index()
  {
    $title = 'Daftar Kelola Ujian';
    $subjects = Subject::all();
    $semesters = Semester::all();
    $gradeLevels = GradeLevel::all();
    $examRules = ExamRules::all();
    $schoolYears = SchoolYear::all();
    $majors = Major::all();
    return view('backend.cbt.manageExam', compact('title', 'subjects', 'semesters', 'gradeLevels', 'examRules', 'schoolYears', 'majors'));
  }

  /**
   * get subject or class
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getSubjectOrClass(Request $request)
  {
    $level = $request->level;
    $majorId = $request->major_id;
    $user = Auth::user();

    if (optional(configuration())->type_school == 1) {
      $subjects = Subject::where('semester_id', $level)
        ->where('major_id', $majorId)
        ->get();
      $classes = StudentClass::where('semester_id', $level)
        ->where('employee_id', $user->employee_id)
        ->get();
    } else {
      $data = Subject::where('grade_level_id', $level)->get();
      if (optional(configuration())->type_school == 2) {
        $data->where('major_id', $majorId);
      }
      $subjects = $data->get();
      $classes = StudentClass::where('grade_level_id', $level)
        ->where('employee_id', $user->employee_id)
        ->get();
    }

    if ($subjects) {
      $json = ['status' => 200, 'subjects' => $subjects, 'classes' => $classes];
    } else {
      $json = ['status' => 500, 'message' => 'Data tidak ditemukan'];
    }

    return response()->json($json);
  }

  /**
   * get amount question
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getAmountQuestion(Request $request)
  {
    $subjectId = $request->subject_id;
    $semesterId = $request->semester_id;
    $gradeLevelId = $request->grade_level_id;
    $questionBank = QuestionBank::where('subject_id', $subjectId)->count();
    $where = (optional(configuration())->type_school == 1)
      ? $where = ['semester_id' => $semesterId]
      : $where = ['grade_level_id' => $gradeLevelId];
    $classes = StudentClass::where($where)
      ->where('subject_id', $subjectId)
      ->where('employee_id', Auth::user()->employee_id)
      ->get();
    return response()->json(['status' => 200, 'data' => $questionBank, 'classes' => $classes]);
  }

  /**
   * get text rules
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getTextRules(Request $request)
  {
    $id = $request->id;
    $data = ExamRules::find($id);
    $convertText = htmlspecialchars($data->text);
    $textReplace = trim(preg_replace('/\s\s+/', ' ', $convertText));

    if ($data) {
      $json = ['status' => 200, 'text' => $textReplace];
    } else {
      $json = ['status' => 500, 'message' => 'Data tidak ditemukan'];
    }

    return response()->json($json);
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
        $updateDelete = checkPermission()->update_delete;
        $update = checkPermission()->update;
        $delete = checkPermission()->delete;
        $button = null;
        $endDate = Carbon::parse($query->end_date)->format('Y-m-d H:i:s');
        $today = Carbon::now()->format('Y-m-d H:i:s');

        if ($query->status == 1) {
          $button .= '<a href="#" class="btn btn-warning btn-sm" id="' . $query->id . '" onclick="showDetail(' . $query->id . ')" title="Detail Ujian"><i class="icon icon-eye"></i></a>
                      <a href="#" class="btn btn-primary btn-sm btn-assign" title="Assign Siswa" id="' . $query->id . '"><i class="icon icon-users"></i></a>
                      <a href="#" class="btn btn-info btn-sm btn-add-student" title="Lihat Siswa" id="' . $query->id . '" onclick="showStudent(' . $query->id . ')"><i class="icon icon-bar-chart"></i></a>';
        } else {
          /* check select question has filled or not */
          if ($query->select_question == 1) {
            $button .= '<a href="#" class="btn btn-warning btn-sm btn-switch" title="Aktif Ujian" id="' . $query->id . '" onclick="activateExam(' . $query->id . ')"><i class="icon icon-power-off"></i></a> ';
          }

          if ($updateDelete) {
            $button .= '<a href="#" class="btn btn-success btn-sm btn-edit" title="Edit Data" id="' . $query->id . '" onclick="editData(' . $query->id . ')"><i class="icon icon-pencil-square-o"></i></a>
                        <a href="#" class="btn btn-primary btn-sm btn-assign" title="Assign Siswa" id="' . $query->id . '"><i class="icon icon-users"></i></a>
                        <a href="#" class="btn btn-info btn-sm btn-select-question" title="Pilih Soal" id="' . $query->id . '" onclick="selectQuestion(' . $query->id . ')"><i class="icon icon-list"></i></a>
                        <a href="#" class="btn btn-danger btn-sm" id="' . $query->id . '" onclick="deleteData(' . $query->id . ')" title="Delete Data"><i class="icon icon-trash-o"></i></a>';
          } else if ($update) {
            $button .= '<a href="#" class="btn btn-success btn-sm btn-edit" title="Edit Data" id="' . $query->id . '" onclick="editData(' . $query->id . ')"><i class="icon icon-pencil-square-o"></i></a>
                        <a href="#" class="btn btn-primary btn-sm btn-assign" title="Assign Siswa" id="' . $query->id . '"><i class="icon icon-users"></i></a>
                        <a href="#" class="btn btn-info btn-sm btn-select-question" title="Pilih Soal" id="' . $query->id . '" onclick="selectQuestion(' . $query->id . ')"><i class="icon icon-list"></i></a>';
          } else if ($delete) {
            $button .= '<a href="#" class="btn btn-danger btn-sm" id="' . $query->id . '" onclick="deleteData(' . $query->id . ')" title="Delete Data"><i class="icon icon-trash-o"></i></a>
                        <a href="#" class="btn btn-primary btn-sm btn-assign" title="Assign Siswa" id="' . $query->id . '"><i class="icon icon-users"></i></a>
                        <a href="#" class="btn btn-info btn-sm btn-select-question" title="Pilih Soal" id="' . $query->id . '" onclick="selectQuestion(' . $query->id . ')"><i class="icon icon-list"></i></a>';
          } else {
            $button .= '<a href="#" class="btn btn-info btn-sm btn-select-question" title="Pilih Soal" id="' . $query->id . '" onclick="selectQuestion(' . $query->id . ')"><i class="icon icon-list"></i></a>
                        <a href="#" class="btn btn-primary btn-sm btn-assign" title="Assign Siswa" id="' . $query->id . '"><i class="icon icon-users"></i></a>';
          }
        }
        return $button;
      })
      ->rawColumns(['name', 'action', 'type_exam'])
      ->make(true);
  }

  /**
   * Show data in datatable assign student.
   *
   */
  public function datatableAssignStudent()
  {
    $data = AssignExamStudent::with(['student'])->get();
    return DataTables::of($data)
      ->addIndexColumn()
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
      ->make(true);
  }

  /**
   * Show temporary question data in datatable.
   * @param Request $request
   * @return
   * @throws \Exception
   */
  public function showQuestionHaveBeenSavedDatatable(Request $request)
  {
    $lockUnlock = $request->lock_unlock;
    $examId = $request->exam_id;
    $data = AccommodateExamQuestion::with(['questionBank.correctAnswer'])
      ->where('exam_id', $examId)
      ->orderBy('id', 'desc')
      ->get();

    return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('checkbox', function ($query) use ($lockUnlock) {
        if ($lockUnlock == 0) {
          return '<center class="text-info" style="font-size: 17px"><i class="icon icon-lock"></i></center>';
        } else if ($lockUnlock == 1) {
          return '<center><label class="custom-control custom-control-danger custom-checkbox">
                  <input class="custom-control-input delete_question" type="checkbox" name="delete_question[]" value="' . $query->id . '">
                  <span class="custom-control-indicator"></span>
                </label></center>';
        } else if ($lockUnlock == 2) {
          return '<center class="text-info" style="font-size: 17px"><i class="icon icon-lock"></i></center>';
        }
      })
      ->addColumn('answer', function ($query) {
        $countCharacter = strlen($query->questionBank->correctAnswer->answer_name);
        $answer = null;

        if ($countCharacter > 50) {
          $answerName = htmlspecialchars($query->questionBank->correctAnswer->answer_name);
          $split = mb_substr($answerName, 0, 50, 'UTF-8');
          $answer = '<div data-toggle="tooltip" data-html="true" data-placement="right" title="' . $answerName . '">' . utf8_decode($split) . '...' . '</div>';
        } else {
          $answer = htmlspecialchars($query->questionBank->correctAnswer->answer_name);
        }

        return $answer;
      })
      ->addColumn('document', function ($query) {
        $document = null;

        if ($query->questionBank->extension == 'mp3' || $query->questionBank->extension == 'ogg' || $query->questionBank->extension == 'wav') {
          $document = '<span class="badge badge-primary">Audio</span>';
        } elseif ($query->questionBank->extension == 'mp4' || $query->questionBank->extension == 'mkv' || $query->questionBank->extension == 'm4a') {
          $document = '<span class="badge badge-primary">Audio</span>';
        } else {
          $document = '-';
        }

        return $document;
      })
      ->rawColumns(['document', 'question', 'answer', 'checkbox'])
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
      ->whereHas('scoreStudents', function ($query) {
        $query->where('remedial_id', null);
        $query->where('supplementary_id', null);
      })
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
    $exam = ManageExam::find($examId);
    $minimalCriteria = MinimalCompletenessCriteria::where('subject_id', $exam->subject_id)->first();
    $data = StudentExamScore::with(['assignStudent.student', 'remedial'])
      ->whereHas('assignStudent', function ($query) use ($studentId) {
        $query->where('student_id', $studentId);
      })
      ->whereHas('assignStudent.exam.examClass', function ($query) use ($exam) {
        $query->where('class_id', $exam->examClass->class_id);
      })
      ->where('remedial_id', null)
      ->where('supplementary_id', null)
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
      ->rawColumns(['score', 'minimal', 'exam_to'])
      ->make(true);
  }

  /**
   * Show question data in datatable.
   * @param Request $request
   * @return
   * @throws \Exception
   */
  public function showQuestionDatatable(Request $request)
  {
    $schoolYearId = $request->school_year_id;
    $examId = $request->exam_id;
    $exam = ManageExam::find($examId);
    $arrayQuestion = [];
    $questions = QuestionBank::with(['correctAnswer'])
      ->where('school_year_id', $schoolYearId)
      ->where('subject_id', $exam->subject_id)
      ->orderBy('id', 'desc')
      ->get();

    foreach ($questions as $d) {
      $accommodateQuestion = AccommodateExamQuestion::where('exam_id', $examId)
        ->where('question_bank_id', $d->id)
        ->get();
      if ($accommodateQuestion->isEmpty()) {
        $arrayQuestion[] = $d;
      }
    }

    $data = [];
    if (!empty($arrayQuestion)) {
      foreach ($arrayQuestion as $q) {
        /* check whether the data has used or not */
        $exists = AccommodateExamQuestion::where('question_bank_id', $q->id)
          ->where('exam_id', $examId)
          ->first();

        if (!$exists) {
          $data[] = $q;
        }
      }
    }

    return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('checkbox', function ($query) {
        return '<label class="custom-control custom-control-primary custom-checkbox">
                  <input class="custom-control-input checkbox_question" type="checkbox" name="checkbox_question[]" value="' . $query->id . '">
                  <span class="custom-control-indicator"></span>
                </label>';
      })
      ->addColumn('answer', function ($query) {
        $answer = null;
        if (!is_null(optional($query->correctAnswer)->answer_name)) {
          $countCharacter = strlen(optional($query->correctAnswer)->answer_name);

          if ($countCharacter > 100) {
            $answerName = htmlspecialchars(optional($query->correctAnswer)->answer_name);
            $answer = '<div data-toggle="tooltip" data-html="true" data-placement="right" title="' . $answerName . '">' . substr($answerName, 0, 50) . '...' . '</div>';
          } else {
            $answer = htmlspecialchars(optional($query->correctAnswer)->answer_name);
          }
        } else {
          $answer = 'Jawaban belum ditentukan';
        }

        return $answer;
      })
      ->addColumn('document', function ($query) {
        $document = null;

        if ($query->extension == 'mp3' || $query->extension == 'ogg' || $query->extension == 'wav') {
          $document = '<span class="badge badge-primary">Audio</span>';
        } elseif ($query->extension == 'mp4' || $query->extension == 'mkv' || $query->extension == 'm4a') {
          $document = '<span class="badge badge-success">Video</span>';
        } else {
          $document = '-';
        }

        return $document;
      })
      ->rawColumns(['question', 'answer', 'checkbox', 'document'])
      ->make(true);
  }

  /**
   * save data accommodate question
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function storeAccommodateQuestion(Request $request)
  {
    $examId = $request->exam_id;
    $selectQuestion = $request->select;
    $schoolYearId = $request->school_year_id;
    $exam = ManageExam::find($examId);
    $data = null;

    if ($selectQuestion == 1) {
      $exam = ManageExam::where('id', $examId)->first();
      $listQuestion = QuestionBank::where('school_year_id', $schoolYearId)
        ->where('subject_id', $exam->subject_id)
        ->get();
    } else {
      $listQuestion = $request->question_list;
    }

    foreach ($listQuestion as $list) {
      $questionBankId = ($selectQuestion == 1) ? $list->id : $list;
      $checkAnswerKey = AnswerKey::where('question_id', $questionBankId)->first();
      $question = QuestionBank::find($questionBankId);

      if (is_null($checkAnswerKey) || empty($checkAnswerKey)) {
        return response()->json(['status' => 500, 'message' => 'Soal ' . html_entity_decode($question->question_name) . ' belum ditentukan jawababnnya']);
      }

      $params = [
        'exam_id' => $examId,
        'question_bank_id' => $questionBankId
      ];
      AccommodateExamQuestion::updateOrCreate($params, $params);
    }

    $data = $exam->update(['select_question' => 1]);

    if ($data) {
      $json = ['status' => 200, 'message' => 'Soal berhasil disimpan'];
    } else {
      $json = ['status' => 500, 'message' => 'Soal gagal disimpan'];
    }

    return response()->json($json);
  }

  /**
   * activate status exam
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function activateExam(Request $request)
  {
    $examId = $request->id;
    $exam = ManageExam::find($examId);
    $checkData = $this->checkingData($examId, $exam); // checking data

    if (!is_null($checkData)) {
      return response()->json(['status' => 500, 'message' => $checkData]);
    }

    $exam->update(['status' => 1]);
    if ($exam) {
      return response()->json(['status' => 200, 'message' => "Ujian berhasil diaktifkan"]);
    } else {
      return response()->json(['status' => 500, 'message' => "Ujian gagal diaktifkan"]);
    }
  }

  /**
   * checking data
   * @param $examId
   * @param $exam
   * @return string
   */
  private function checkingData($examId, $exam)
  {
    $message = null;
    $countAssignExamStudent = AssignExamStudent::where('exam_id', $examId)->count();
    $countAssignExamStudentAfterGenerate = AssignExamStudent::where('exam_id', $examId)
      ->where('status_generate', 1)
      ->count();
    $questionForStudent = QuestionForStudent::whereHas('accommodateExamQuestion', function ($query) use ($examId) {
      $query->where('exam_id', $examId);
    })
      ->get();

    /* check whether student have been assign or not */
    if ($exam->assignStudent->isEmpty()) {
      $message = "Belum ada siswa yang di assign";
    }

    /* check whether question have been select or not */
    if ($exam->select_question != 1) {
      $message = "Silahkan pilih soal terlebih dahulu";
    }

    /* check whether question have been select or not */
    if ($countAssignExamStudent != $countAssignExamStudentAfterGenerate) {
      $count = $countAssignExamStudent - $countAssignExamStudentAfterGenerate;
      $message = "Terdapat " . $count . " siswa belum digenerate soalnya";
    }

    /* check whether exam question have been generate or not */
    if (is_null($questionForStudent)) {
      $message = 'Silahkan generate soal untuk siswa terlebih dahulu';
    }

    return $message;
  }

  /**
   * detail exam
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(Request $request)
  {
    $examId = $request->id;
    $exam = ManageExam::where('id', $examId)->first();
    $level = (!is_null($exam->semester_id)) ? $exam->semester->number : $exam->gradeLevel->name;
    $startDate = date('d', strtotime($exam->start_date)) . ' ' . convertMonthName(date('m', strtotime($exam->start_date))) . ' ' . date('Y', strtotime($exam->start_date)) . ' ' . date('H:i', strtotime($exam->start_date));
    $endDate = date('d', strtotime($exam->end_date)) . ' ' . convertMonthName(date('m', strtotime($exam->end_date))) . ' ' . date('Y', strtotime($exam->end_date)) . ' ' . date('H:i', strtotime($exam->end_date));
    $time = $startDate . ' Sampai ' . $endDate;
    $showValue = ($exam->show_value == 1) ? 'Ya' : 'Tidak';
    $type = null;

    if ($exam->type_exam == 1) {
      $type = 'Ulangan Harian';
    } else if ($exam->type_exam == 2) {
      $type = 'Ujian Tengah Semester';
    } else if ($exam->type_exam == 3) {
      $type = 'Ujian Akhir Semester';
    } else {
      $type = 'Try Out';
    }

    $data = [
      'exam_name' => $exam->name,
      'level' => $level,
      'subject_name' => $exam->subject->name,
      'type_exam_detail' => $type,
      'time' => $time,
      'student_class' => $exam->examClass->studentClass->className,
      'amount_question_detail' => $exam->amount_question . ' Buah',
      'duration_detail' => $exam->duration . ' Menit',
      'time_violation_detail' => $exam->time_violation . ' Detik',
      'show_value_detail' => $showValue,
      'type_rules' => $exam->examRules->name
    ];

    if ($exam) {
      return response()->json(['status' => 200, 'data' => $data]);
    } else {
      return response()->json(['status' => 500, 'message' => "Data tidak ditemukan"]);
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param ManageExamRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(ManageExamRequest $request)
  {
    $semesterId = $request->semester_id;
    $gradeLevelId = $request->grade_level_id;
    $examRulesId = $request->exam_rules_id;
    $classId = $request->class_id;
    $majorId = $request->major_id;
    $subjectId = $request->subject_id;
    $typeExam = $request->type_exam;
    $duration = $request->duration;
    $timeViolation = $request->time_violation;
    $amountQuestion = $request->amount_question;
    $showValue = $request->show_value;
    $name = $request->name;
    $startDate = $request->start_date;
    $endDate = $request->end_date;
    $startTime = $request->start_time;
    $endTime = $request->end_time;
    $countAmountQuestionBank = QuestionBank::where('subject_id', $subjectId)->count();
    $examRules = ExamRules::where('id', $examRulesId)->first();
    $mergeStartDate = $startDate . ' ' . $startTime;
    $mergeEndDate = $endDate . ' ' . $endTime;
    $minimalCriteria = MinimalCompletenessCriteria::where('subject_id', $subjectId)->first();
    $checkDataMessage = $this->checkData($examRules, $mergeStartDate, $mergeEndDate, $amountQuestion, $countAmountQuestionBank, $minimalCriteria);

    /* check data */
    if (!is_null($checkDataMessage)) {
      return response()->json(['status' => 500, 'message' => $checkDataMessage]);
    }

    $insert = ManageExam::create([
      'semester_id' => $semesterId,
      'grade_level_id' => $gradeLevelId,
      'exam_rules_id' => $examRulesId,
      'subject_id' => $subjectId,
      'major_id' => $majorId,
      'type_exam' => $typeExam,
      'duration' => $duration,
      'status' => 0,
      'time_violation' => $timeViolation,
      'amount_question' => $amountQuestion,
      'show_value' => $showValue,
      'start_date' => $startDate . " " . $startTime,
      'end_date' => $endDate . " " . $endTime,
      'name' => $name,
      'employee_id' => Auth::user()->employee_id,
      'created_by' => Auth::user()->employee_id
    ]);

    /* insert to table exam class transaction */
    $this->storeExamClass($classId, $insert, "insert");

    if ($insert) {
      $json = ['status' => 200, 'message' => 'Data berhasil disimpan'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal disimpan'];
    }

    return response()->json($json);
  }

  /**
   * check data
   * @param $examRules
   * @param $startDate
   * @param $endDate
   * @param $amountQuestion
   * @param $countAmountQuestionBank
   * @param $minimal
   * @return string
   */
  private function checkData($examRules, $startDate, $endDate, $amountQuestion, $countAmountQuestionBank, $minimal)
  {
    $message = null;
    /* check exam rules */
    if (is_null($examRules->text)) {
      $message = "Silahkan isi text terlebih dahulu";
    }

    /* check whether minimal criteria for this subject have been filled or not */
    if (is_null($minimal)) {
      $message = "KKM untuk " . subjectName() . " ini belum ditentukan, harap hubungi admin";
    }

    /* check whether end date is smaller than start date */
    if ($startDate >= $endDate) {
      $message = "Tanggal dan Waktu Selesai minimal harus lebih dari tanggal dan waktu mulai";
    }

    /* check whether amount question is greater than amount question in question bank */
    if ($amountQuestion > $countAmountQuestionBank) {
      $message = "Jumlah soal tidak boleh lebih dari jumlah di bank soal";
    }

    /* check whether amount question is equals 0 nor not */
    if ($amountQuestion == 0) {
      $message = "Jumlah soal tidak boleh 0";
    }
    return $message;
  }

  /**
   * store exam class
   * @param $classes
   * @param $id
   * @param $type
   */
  private function storeExamClass($classes, $id, $type = null)
  {
    if ($type == "insert") {
      ExamClassTransaction::create([
        'exam_id' => $id->id,
        'class_id' => $classes,
      ]);
    } else {
      ExamClassTransaction::find($id)->update([
        'class_id' => $classes
      ]);
    }
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function edit(Request $request)
  {
    $id = $request->id;
    $data = ManageExam::with('examClass')->where('id', $id)->first();
    $examRules = ExamRules::find($data->exam_rules_id);
    $subjects = Subject::where('semester_id', optional($data)->semester_id)
      ->orWhere('semester_id', null)
      ->get();
    $classes = StudentClass::where('employee_id', Auth::user()->employee_id)
      ->where('subject_id', $data->subject_id)
      ->get();
    $questionBank = QuestionBank::where('subject_id', $data->subject_id)->count();
    $convertText = htmlspecialchars($examRules->text);
    $textReplace = trim(preg_replace('/\s\s+/', ' ', $convertText));

    if ($data) {
      $json = ['status' => 200, 'data' => $data, 'text' => $textReplace, 'subjects' => $subjects, 'count' => $questionBank, 'classes' => $classes];
    } else {
      $json = ['status' => 500, 'message' => 'Data tidak ditemukan'];
    }

    return response()->json($json);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param ManageExamRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(ManageExamRequest $request)
  {
    $id = $request->id;
    $semesterId = $request->semester_id;
    $gradeLevelId = $request->grade_level_id;
    $examRulesId = $request->exam_rules_id;
    $subjectId = $request->subject_id;
    $classId = $request->class_id;
    $majorId = $request->major_id;
    $typeExam = $request->type_exam;
    $duration = $request->duration;
    $timeViolation = $request->time_violation;
    $amountQuestion = $request->amount_question;
    $showValue = $request->show_value;
    $name = $request->name;
    $startDate = $request->start_date;
    $endDate = $request->end_date;
    $startTime = $request->start_time;
    $endTime = $request->end_time;
    $countAmountQuestionBank = QuestionBank::where('subject_id', $subjectId)->count();

    /* check whether end date is smaller than start date */
    if (date("Y-m-d H:i", strtotime($startDate . ' ' . $startTime)) >= date("Y-m-d H:i", strtotime($endDate . ' ' . $endTime))) {
      return response()->json(['status' => 500, 'message' => "Tanggal dan Waktu Selesai minimal harus lebih dari tanggal dan waktu mulai."]);
    }

    /* check whether amount question is greater than amount question in question bank */
    if ($amountQuestion > $countAmountQuestionBank) {
      return response()->json(['status' => 500, 'message' => "Jumlah soal tidak boleh lebih dari jumlah di bank soal."]);
    }

    /* check whether amount question is equals 0 nor not */
    if ($amountQuestion == 0) {
      return response()->json(['status' => 500, 'message' => "Jumlah soal tidak boleh 0"]);
    }

    $update = ManageExam::find($id)->update([
      'semester_id' => $semesterId,
      'grade_level_id' => $gradeLevelId,
      'exam_rules_id' => $examRulesId,
      'major_id' => $majorId,
      'subject_id' => $subjectId,
      'type_exam' => $typeExam,
      'duration' => $duration,
      'time_violation' => $timeViolation,
      'amount_question' => $amountQuestion,
      'show_value' => $showValue,
      'start_date' => $startDate . " " . $startTime,
      'end_date' => $endDate . " " . $endTime,
      'name' => $name,
      'last_updated_by' => Auth::user()->employee_id
    ]);

    /* update data exam class transaction */
    $this->storeExamClass($classId, $id);

    if ($update) {
      $json = ['status' => 200, 'message' => 'Data berhasil diubah'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal diubah'];
    }

    return response()->json($json);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Request $request)
  {
    $id = $request->id;
    $delete = ManageExam::find($id);
    $delete->delete();

    if ($delete) {
      $json = ['status' => 200, 'message' => 'Data berhasil dihapus'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal dihapus'];
    }

    return response()->json($json);
  }

  /**
   * destroy temporary question by id
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroyTemporaryQuestionById(Request $request)
  {
    $questions = $request->question_list;
    $countQuestion = count($questions);
    $delete = AccommodateTemporaryExamQuestion::whereIn('id', $questions)->delete();

    if ($delete) {
      $json = ['status' => 200, 'message' => $countQuestion . ' soal berhasil dihapus'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal dihapus'];
    }

    return response()->json($json);
  }

  /**
   * get exam by class
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getExamByClass(Request $request)
  {
    $subjectName = subjectName();
    $level = level();
    $classId = $request->class_id;
    if (Auth::guard('employee')->check()) {
      $exams = ManageExam::with(['subject', 'semester', 'gradeLevel'])
        ->whereHas('examClass', function ($query) use ($classId) {
          $query->where('class_id', $classId);
        })
        ->orderBy('id', 'desc')
        ->paginate(5);
    } else {
      $exams = ManageExam::with(['subject', 'semester', 'gradeLevel'])
        ->with(['singleAssignStudent' => function ($query) {
          $query->with(['scoreStudent']);
          $query->where('student_id', Auth::user()->student_id);
        }])
        ->whereHas('examClass', function ($query) use ($classId) {
          $query->where('class_id', $classId);
        })
        ->whereHas('singleAssignStudent', function ($query) use ($classId) {
          $query->where('student_id', Auth::user()->student_id);
          $query->whereHas('questionExamStudent', function ($params) {
            $params->where('supplementary_exam_id', null);
          });
        })
        ->orderBy('id', 'desc')
        ->paginate(5);
    }
    return response()->json(['status' => 200, 'data' => $exams, 'subject_name' => $subjectName, 'level' => $level]);
  }
}
