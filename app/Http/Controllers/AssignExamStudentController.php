<?php

namespace App\Http\Controllers;

use App\Models\AccommodateExamQuestion;
use App\Models\AssignExamStudent;
use App\Models\ExamClassTransaction;
use App\Models\ManageExam;
use App\Models\QuestionForStudent;
use App\Models\Student;
use App\Models\StudentExamScore;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AssignExamStudentController extends Controller
{
  /**
   * datatable search student
   * @param Request $request
   * @return
   * @throws \Exception
   */
  public function datatableSearchStudent(Request $request)
  {
    $examId = $request->exam_id;
    $examClass = ExamClassTransaction::where('exam_id', $examId)->first();
    $studentClassTransactions = Student::with('examStudent')
      ->whereHas('studentClassTransaction', function ($query) use ($examClass) {
        $query->where('class_id', $examClass->class_id);
      })->get();

    $arrayStudents = [];
    foreach ($studentClassTransactions as $studentClassTransaction) {
      $assignExams = AssignExamStudent::where('exam_id', $examId)
        ->where('student_id', $studentClassTransaction->id)
        ->get();
      if ($assignExams->isEmpty()) {
        $arrayStudents[] = $studentClassTransaction;
      }
    }

    $data = [];
    if (!empty($arrayStudents)) {
      foreach ($arrayStudents as $arrayStudent) {
        /* check whether the data has used or not */
        $exists = AssignExamStudent::where('student_id', $arrayStudent->student_id)
          ->where('exam_id', $examId)
          ->first();

        if (!$exists) {
          $data[] = $arrayStudent;
        }
      }
    }

    return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('checkbox', function ($query) {
        return '<label class="custom-control custom-control-primary custom-checkbox">
                  <input class="custom-control-input checkbox_student" type="checkbox" name="checkbox_student[]" value="' . $query->id . '">
                  <span class="custom-control-indicator"></span>
                </label>';
      })
      ->rawColumns(['checkbox'])
      ->make(true);
  }

  /**
   * datatable search student
   * @param Request $request
   * @return
   * @throws \Exception
   */
  public function datatableAssignStudent(Request $request)
  {
    $examId = $request->exam_id;
    $assignExamStudent = AssignExamStudent::with(['student'])
      ->where('exam_id', $examId)
      ->orderBy('id', 'desc')
      ->get();

    return DataTables::of($assignExamStudent)
      ->addIndexColumn()
      ->addColumn('checkbox', function ($query) {
        if ($query->exam->status == 1) {
          return '<center class="text-info" style="font-size: 17px"><i class="icon icon-lock"></i></center>';
        } else {
          return '<center>
                    <label class="custom-control custom-control-danger custom-checkbox">
                      <input class="custom-control-input checkbox_assign_student" type="checkbox" name="checkbox_assign_student[]" value="' . $query->id . '">
                      <span class="custom-control-indicator"></span>
                    </label>
                  </center>';
        }
      })
      ->addColumn('status', function ($query) {
        if ($query->status == 0) {
          return '<span class="label label-danger">Belum Ujian</span>';
        } else {
          return '<span class="label label-primary">Sudah Ujian</span>';
        }
      })
      ->addColumn('score', function ($query) {
        $score = StudentExamScore::where('assign_student_id', $query->id)
          ->where('remedial_id', null)
          ->where('supplementary_id', null)
          ->first();

        if (is_null($score)) {
          return '-';
        } else {
          return '<b style="font-size: 15px">' . $score->score . '</b>';
        }
      })
      ->rawColumns(['checkbox', 'status', 'score'])
      ->make(true);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(Request $request)
  {
    $examId = $request->exam_id;
    $type = $request->type;
    $insert = null;

    /* 1 = all student, 2 = certain student */
    if ($type == 1) {
      $examClass = ExamClassTransaction::where('exam_id', $examId)->first();
      $students = Student::with('examStudent')
        ->whereHas('studentClassTransaction', function ($query) use ($examClass) {
          $query->where('class_id', $examClass->class_id);
        })->get();
    } else {
      $students = $request->student_id;
    }

    foreach ($students as $student) {
      $studentId = ($type == 1) ? $student->id : $student;
      $statusGenerate = (is_null(optional($student)->examStudent)) ? 0 : optional($student->examStudent)->status_generate;
      $where = [
        'exam_id' => $examId,
        'student_id' => $studentId,
      ];
      $data = [
        'exam_id' => $examId,
        'student_id' => $studentId,
        'status' => 0,
        'status_generate' => ($type == 1) ? $statusGenerate : 0,
        'violation' => 0,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ];
      $insert = AssignExamStudent::updateOrCreate($where, $data);
    }

    if ($insert) {
      $json = ['status' => 200, 'message' => 'Data berhasil disimpan'];
    } else {
      $json = ['status' => 200, 'message' => 'Data gagal disimpan'];
    }
    return response()->json($json);
  }

  /**
   * checking data
   * @param $examId
   * @return string
   */
  private function checkingData($examId)
  {
    $checkQuestion = AccommodateExamQuestion::where('exam_id', $examId)->count();
    $checkAssignStudent = AssignExamStudent::where('exam_id', $examId)->count();
    $exam = ManageExam::where('id', $examId)->first();
    $message = null;

    /* check whether students have been assigned to the exam or not */
    if ($checkAssignStudent == 0) {
      $message = 'Silahkan tambah siswa terlebih dahulu';
    }

    /* check question exam have been select or not */
    if ($checkQuestion == 0) {
      $message = 'Silahkan pilih soal terlebih dahulu';
    }

    /* check whether the exam questions are less than the number of questions or not */
    if ($checkQuestion < $exam->amount_question) {
      $count = $exam->amount_question - $checkQuestion;
      $message = 'Soal tidak boleh kurang dari jumlah soal, masih kurang ' . $count . ' soal lagi';
    }
    return $message;
  }

  /**
   * generate random question
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function generateRandomQuestion(Request $request)
  {
    $examId = $request->exam_id;
    $exam = ManageExam::where('id', $examId)->first();
    $assignStudents = $exam->assignStudent()->where('status_generate', 0)->get();
    $message = $this->checkingData($examId); // checking data;
    $data = [];

    if (!is_null($message)) {
      return response()->json(['status' => 500, 'message' => $message]);
    }

    $stored = 0;
    DB::transaction(function () use($assignStudents, $data, $exam, $examId, &$stored) {
      foreach ($assignStudents as $assign) {
        $questionExams = $exam->question()->inRandomOrder()->limit($exam->amount_question)->get();
        foreach ($questionExams as $question) {
          $data[] = [
            'exam_question_id' => $question->id,
            'assign_student_id' => $assign->id,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
          ];
        }
        $exam->assignStudent()->where('student_id', $assign->student_id)->update(['status_generate' => 1]);
      }

      if (!empty($data)) {
        QuestionForStudent::insert($data);
        $stored++;
      }

      try {
        DB::commit();
        return true;
      } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['status' => 500, 'message' => 'Terjadi kesalahan pada server']);
      }
    }, 3);

    if ($stored > 0) {
      $json = ['status' => 200, 'message' => 'Berhasil generate soal untuk ' . $assignStudents->count() . ' siswa'];
    } else {
      $json = ['status' => 500, 'message' => 'Siswa sudah digenerate semua'];
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
    $countStudent = count($id);
    $delete = AssignExamStudent::whereIn('id', $id)->delete();
    QuestionForStudent::where('assign_student_id', $id)->delete();

    if ($delete) {
      $json = ['status' => 200, 'message' => $countStudent . ' siswa berhasil dihapus'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal dihapus'];
    }

    return response()->json($json);
  }
}
