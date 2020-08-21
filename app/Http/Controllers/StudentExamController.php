<?php

namespace App\Http\Controllers;

use App\Models\AccommodateExamStudentAnswer;
use App\Models\AnswerKey;
use App\Models\AssignExamStudent;
use App\Models\ManageExam;
use App\Models\MinimalCompletenessCriteria;
use App\Models\QuestionForStudent;
use App\Models\RemedialExam;
use App\Models\StudentClass;
use App\Models\StudentExamScore;
use App\Models\SupplementaryExam;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentExamController extends Controller
{
  /**
   * school config
   * @param Request $request
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   */
  public function getSchoolConfig(Request $request)
  {
    $examId = $request->exam_id;
    $config = configuration();
    $exam = ManageExam::where('id', $examId)->first();
    $class = StudentClass::with(['subject', 'schoolYear'])
      ->where('id', $exam->examClass->class_id)
      ->first();
    return response(['status' => 200, 'config' => $config, 'class' => $class, 'exam' => $exam]);
  }

  /**
   * get exam Rules
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getExamRules(Request $request)
  {
    $examId = $request->exam_id;
    $exam = ManageExam::with(['examRules'])->where('id', $examId)->first();
    $text = htmlspecialchars($exam->examRules->text);
    $replaceText = trim(preg_replace('/\s\s+/', ' ', $text));
    return response()->json(['status' => 500, 'text' => $replaceText]);
  }

  /**
   * get question and answer by student
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getQuestionAndAnswer(Request $request)
  {
    $examId = $request->exam_id;
    $exam = ManageExam::with(['subject'])->where('id', $examId)->first();
    $studentId = Auth::user()->student_id;
    $assignStudent = AssignExamStudent::where('exam_id', $examId)->where('student_id', $studentId)->first();

    $questionStudent = QuestionForStudent::with([
      'accommodateExamQuestion.questionBank.answerKey',
      'accommodateExamQuestion.questionBank.studentAnswer' => function ($query) use ($studentId) {
        $query->where('student_id', $studentId);
      }])
      ->where('assign_student_id', $assignStudent->id);

    return response()->json([
      'status' => 200,
      'all' => $questionStudent->get(),
      'question' => $questionStudent->paginate(1),
      'exam' => $exam,
    ]);
  }

  /**
   * start student exam
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function startExam(Request $request)
  {
    $examId = $request->exam_id;
    $student = AssignExamStudent::where('exam_id', $examId)
      ->where('student_id', Auth::user()->student_id)
      ->first();

    if (is_null($student->ip_address)) {
      $student->update(['ip_address' => $request->ip()]);
    } elseif ($request->ip() == $student->ip_address) {
      return response()->json(['status' => 200, 'exam_page' => URL('/exam/page/' . $examId)]);
    } else {
      return response()->json(['status' => 500, 'message' => 'Anda sudah memulai ujian']);
    }

    return response()->json(['status' => 200, 'exam_page' => URL('/exam/page/' . $examId)]);
  }

  /**
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function answerQuestionStudent(Request $request)
  {
    $examId = $request->exam_id;
    $questionId = $request->question_id;
    $answerId = $request->answer_id;
    $studentId = Auth::user()->student_id;
    $type = $request->type;
    $assignStudent = AssignExamStudent::where('exam_id', $examId)
      ->where('student_id', $studentId)
      ->first();
    $studentAnswer = AccommodateExamStudentAnswer::where('exam_id', $examId)
      ->where('question_id', $questionId)
      ->where('student_id', $studentId)
      ->first();
    $remedialOrSupplementary = $this->checkRemedialOrSupplementary($type, $assignStudent);

    DB::transaction(function () use ($studentAnswer, $examId, $questionId, $studentId, $answerId, $remedialOrSupplementary) {
      if (is_null($studentAnswer)) {
        AccommodateExamStudentAnswer::create([
          'exam_id' => $examId,
          'question_id' => $questionId,
          'student_id' => $studentId,
          'answer_id' => $answerId,
          'remedial_id' => $remedialOrSupplementary->remedial,
          'supplementary_id' => $remedialOrSupplementary->supplementary
        ]);
      } else {
        AccommodateExamStudentAnswer::where('exam_id', $examId)
          ->where('question_id', $questionId)
          ->where('student_id', $studentId)
          ->update(['answer_id' => $answerId]);
      }

      try {
        DB::commit();
        return true;
      } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['status' => 500, 'message' => 'Terjadi kesalahan pada server']);
      }
    });

    $questionStudents = $this->getDataAfterAnsweringQuestion($studentId, $examId);
    $countStudentAnswer = $this->countStudentAnswer($type, $examId, $studentId, $remedialOrSupplementary->remedial, $remedialOrSupplementary->supplementary);
    return response()->json([
      'status' => 200,
      'message' => 'Jawaban berhasil disimpan',
      'all' => $questionStudents,
      'count' => $countStudentAnswer
    ]);
  }

  /**
   * hesitate answer question student
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function hesitateAnswerQuestionStudent(Request $request)
  {
    $examId = $request->exam_id;
    $questionId = $request->question_id;
    $hesitate = $request->hesitate;
    $studentId = Auth::user()->student_id;
    $type = $request->type;
    $assignStudent = AssignExamStudent::where('exam_id', $examId)
      ->where('student_id', $studentId)
      ->first();
    $studentAnswer = AccommodateExamStudentAnswer::where('exam_id', $examId)
      ->where('question_id', $questionId)
      ->where('student_id', $studentId)
      ->first();
    $remedialOrSupplementary = $this->checkRemedialOrSupplementary($type, $assignStudent);

    DB::transaction(function () use ($studentAnswer, $examId, $questionId, $studentId, $hesitate, $remedialOrSupplementary) {
      if (is_null($studentAnswer)) {
        AccommodateExamStudentAnswer::create([
          'exam_id' => $examId,
          'question_id' => $questionId,
          'student_id' => $studentId,
          'remedial_id' => $remedialOrSupplementary->remedial,
          'supplementary_id' => $remedialOrSupplementary->supplementary,
          'hesitate' => 1,
        ]);
      } else {
        AccommodateExamStudentAnswer::where('exam_id', $examId)
          ->where('question_id', $questionId)
          ->where('student_id', $studentId)
          ->update(['hesitate' => ($hesitate) ? 1 : null]);
      }

      try {
        DB::commit();
        return true;
      } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['status' => 500, 'message' => 'Terjadi kesalahan pada server']);
      }
    });

    $questionStudents = $this->getDataAfterAnsweringQuestion($studentId, $examId);
    return response()->json(['status' => 200, 'all' => $questionStudents]);
  }

  /**
   * count student answer
   * @param $type
   * @param $examId
   * @param $studentId
   * @param null $remedialId
   * @param null $supplementary
   * @return
   */
  private function countStudentAnswer($type, $examId, $studentId, $remedialId = null, $supplementary = null)
  {
    $countStudentAnswer = AccommodateExamStudentAnswer::where('exam_id', $examId)
      ->where('student_id', $studentId);

    if ($type == 'remedial') {
      $countStudentAnswer->where('remedial_id', $remedialId);
    } else if ($type == 'supplementary') {
      $countStudentAnswer->where('supplementary_id', $supplementary);
    }

    return $countStudentAnswer->count();
  }

  /**
   * check remedial or supplementary
   * @param $type
   * @param $assignStudent
   * @return object
   */
  private function checkRemedialOrSupplementary($type, $assignStudent)
  {
    /* check type remedial or supplementary exam */
    if ($type == 'remedial') {
      $remedial = RemedialExam::where('assign_student_id', $assignStudent->id)
        ->orderBy('id', 'desc')
        ->first()->id;
      $supplementaryExam = null;
    } else if ($type == 'supplementary') {
      $supplementaryExam = SupplementaryExam::where('assign_student_id', $assignStudent->id)
        ->first()->id;
      $remedial = null;
    } else {
      $remedial = null;
      $supplementaryExam = null;
    }

    return (object) [
      'remedial' => $remedial,
      'supplementary' => $supplementaryExam
    ];
  }

  /**
   * get data after answering the question
   * @param $studentId
   * @param $examId
   * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
   */
  private function getDataAfterAnsweringQuestion($studentId, $examId)
  {
    $assignStudent = AssignExamStudent::where('exam_id', $examId)->where('student_id', $studentId)->first();
    return QuestionForStudent::with(['accommodateExamQuestion.questionBank.studentAnswer' => function ($query) use ($studentId) {
      $query->where('student_id', $studentId);
    }])
      ->where('assign_student_id', $assignStudent->id)
      ->get();
  }

  /**
   * count student exam violation
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function countViolation(Request $request)
  {
    $examId = $request->exam_id;
    $studentId = Auth::user()->student_id;
    $assignStudent = AssignExamStudent::with('exam')
      ->where('exam_id', $examId)->where('student_id', $studentId)
      ->first();
    $assignStudent->update(['violation' => ($assignStudent->violation + 1)]);
    return response()->json(['status' => 200, 'data' => $assignStudent]);
  }

  /**
   * add violation student
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function addViolation(Request $request)
  {
    $examId = $request->exam_id;
    $violationName = $request->violation_name;
    $studentId = Auth::user()->student_id;
    $assignStudent = AssignExamStudent::where('exam_id', $examId)
      ->where('student_id', $studentId)
      ->first();
    $assignStudent->violationStudent()->create([
      'assign_student_id' => $assignStudent->id,
      'violation_name' => $violationName
    ]);
    return response()->json(['status' => 200]);
  }

  /**
   * store all student answers
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function storeAllStudentAnswer(Request $request)
  {
    $correctAnswer = 0;
    $examId = $request->exam_id;
    $type = $request->type;
    $studentId = Auth::user()->student_id;
    $exam = ManageExam::where('id', $examId)->first();
    $assignStudent = AssignExamStudent::where('exam_id', $examId)
      ->where('student_id', $studentId)
      ->first();
    $studentAnswers = AccommodateExamStudentAnswer::where('exam_id', $examId)
      ->where('student_id', $studentId)
      ->where('answer_id', '!=', null)
      ->get();
    $remedialOrSupplementary = $this->checkRemedialOrSupplementary($type, $assignStudent);

    $insert = DB::transaction(function () use ($studentAnswers, $exam, $correctAnswer, $assignStudent, $remedialOrSupplementary, $type) {
      /* check answer whether is empty or not */
      if ($studentAnswers->isEmpty()) {
        $score = 0;
      } else {
        foreach ($studentAnswers as $studentAnswer) {
          $answerKey = AnswerKey::where('question_id', $studentAnswer->question_id)
            ->where('key', 1)
            ->first();
          if ($answerKey->id == $studentAnswer->answer_id) {
            $correctAnswer++;
          }
        }
        $score = round((($correctAnswer * 100) / $exam->amount_question), 2);
      }

      $this->storeStudentScore($remedialOrSupplementary, $assignStudent, $score);
      $this->updateStatus($type, $assignStudent);

      try {
        DB::commit();
        return true;
      } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['status' => 500, 'message' => 'Terjadi kesalahan pada server']);
      }
    }, 3);

    if ($insert) {
      $classId = $exam->examClass->class_id;
      $explode = explode(' ', $exam->subject->name);
      $subjectId = implode('-', $explode);
      return response()->json([
        'status' => 200,
        'message' => 'Ujian berhasil disimpan',
        'link' => URL('/detail/' . $classId . '/' . $subjectId),
      ]);
    } else {
      return response()->json(['status' => 500, 'message' => 'Terjadi kesalahan pada server']);
    }
  }

  /**
   * store student score
   * @param $remedialOrSupplementary
   * @param $assignStudent
   * @param $score
   */
  private function storeStudentScore($remedialOrSupplementary, $assignStudent, $score)
  {
    $remedial = $remedialOrSupplementary->remedial;
    $supplementary = $remedialOrSupplementary->supplementary;
    if (is_null($remedial) && is_null($supplementary)) {
      if (is_null($assignStudent->scoreStudent) || empty($assignStudent->scoreStudent)) {
        $assignStudent->scoreStudent()->create([
          'assign_student_id' => $assignStudent->id,
          'score' => $score,
          'violation' => $assignStudent->violation,
        ]);
      } else {
        $assignStudent->scoreStudent()->update([
          'assign_student_id' => $assignStudent->id,
          'score' => $score,
          'violation' => $assignStudent->violation
        ]);
      }
    } else {
      $assignStudent->scoreStudent()->create([
        'assign_student_id' => $assignStudent->id,
        'score' => $score,
        'violation' => $assignStudent->violation,
        'remedial_id' => $remedial,
        'supplementary_id' => $supplementary
      ]);
    }
  }

  /**
   * update status
   * @param $type
   * @param $assignStudent
   */
  private function updateStatus($type, $assignStudent)
  {
    if ($type == 'remedial') {
      RemedialExam::where('assign_student_id', $assignStudent->id)
        ->orderBy('id', 'desc')
        ->update(['status' => 1]);
    } else if ($type == 'supplementary') {
      SupplementaryExam::where('assign_student_id', $assignStudent->id)
        ->update(['status' => 1]);
    }
    $assignStudent->update(['status' => 1]);
  }

  /**
   * get score student exam
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getScoreStudentExam(Request $request)
  {
    $examId = $request->exam_id;
    $studentId = Auth::user()->student_id;
    $exam = ManageExam::where('id', $examId)->first();
    $minimumCriteria = MinimalCompletenessCriteria::where('subject_id', $exam->subject_id)->first();
    $assignStudent = AssignExamStudent::where('exam_id', $examId)
      ->where('student_id', $studentId)
      ->first();
    $scoreStudentExam = $assignStudent->scoreStudent->score;
    return response()->json(['status' => 200, 'score' => $scoreStudentExam, 'minimal' => optional($minimumCriteria)->minimal_criteria]);
  }

  /**
   * get all score student exam
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getAllScoreStudentExam(Request $request)
  {
    $data = [];
    $examId = $request->exam_id;
    $assignStudents = AssignExamStudent::with(['student'])
      ->where('exam_id', $examId)
      ->get();
    foreach ($assignStudents as $assignStudent) {
      $scoreStudents = StudentExamScore::where('assign_student_id', $assignStudent->id)
        ->groupBy('assign_student_id')
        ->max('score');
      $data[] = [
        'photo' => (is_null($assignStudent->student->photo)) ? null : asset('storage/' . $assignStudent->student->photo),
        'name' => $assignStudent->student->name,
        'sin_name' => $assignStudent->student->student_identity_number . ' - ' . $assignStudent->student->name,
        'score' => $scoreStudents
      ];
    }
    return response()->json(['status' => 200, 'data' => $data]);
  }

  /**
   * chart score exam student
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function chartScoreExam(Request $request)
  {
    $pass = [];
    $minimal = [];
    $notPass = [];
    $notYetExam = [];
    $notExam = [];
    $examId = $request->exam_id;
    $exam = ManageExam::where('id', $examId)->first();
    $minimalCriteria = MinimalCompletenessCriteria::where('subject_id', $exam->subject_id)->first();
    $assignStudents = AssignExamStudent::with(['student'])->where('exam_id', $examId)->get();
    $today = Carbon::now()->format('Y-m-d H:i:s');
    $endDate = Carbon::parse($exam->end_date)->format('Y-m-d H:i:s');

    /* check whether minimal criteria is null or not */
    if (is_null($minimalCriteria)) {
      return response()->json(['status' => 500, 'message' => 'KKM belum ditentukan, silahkan hubungi admin']);
    }

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

    $data = [
      'pass' => count($pass),
      'minimal' => count($minimal),
      'not_pass' => count($notPass),
      'not_yet' => count($notYetExam),
      'not_exam' => count($notExam)
    ];
    return response()->json(['status' => 200, 'data' => $data]);
  }
}
