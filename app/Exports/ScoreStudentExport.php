<?php


namespace App\Exports;


use App\Models\AssignExamStudent;
use App\Models\ManageExam;
use App\Models\StudentExamScore;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;

class ScoreStudentExport implements FromView, WithMultipleSheets
{
  use Exportable;

  private $examId;

  public function __construct($examId)
  {
    $this->examId = $examId;
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        $cellRange = 'A1:W1'; // All headers
        $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
      },
    ];
  }

  public function view(): View
  {
    $today = Carbon::now()->toDayDateTimeString();
    $exam = ManageExam::with(['subject', 'semester', 'gradeLevel', 'examClass.studentClass'])
      ->where('id', $this->examId)
      ->first();
    $assignStudents = AssignExamStudent::with(['student', 'exam.semester', 'exam.gradeLevel'])
      ->where('exam_id', $this->examId)
      ->get();
    $collection = [];

    foreach ($assignStudents as $assignStudent) {
      $studentScore = StudentExamScore::where('assign_student_id', $assignStudent->id)
        ->groupBy('assign_student_id');
      $status = $this->checkStatus($studentScore->first(), $today, $exam);
      $score = $studentScore->max('score');
      $collection[] = (object) [
        'sin' => $assignStudent->student->student_identity_number,
        'name' => $assignStudent->student->name,
        'class' => $exam->examClass->studentClass->class_name,
        'status' => $status,
        'score' => $score
      ];
    }
    $data = collect($collection)->sortBy('name');
    return view('backend.cbt.scoreStudentExcel', compact('data', 'exam'));
  }

  public function sheets(): array
  {
    $sheets = [];
    $sheets[] = new ScoreStudentExport($this->examId);
    return $sheets;
  }

  private function checkStatus($score, $today, $exam)
  {
    $status = null;

    if (is_null($score)) {
      if ($today > $exam->end_date) {
        $status = 'Tidak melakukan ujian';
      } elseif ($today < $exam->start_date) {
        $status = 'Ujian belum dimulai';
      } else {
        $status = 'Belum melakukan ujian';
      }
    } else {
      $status = 'Telah melakukan ujian';
    }
    return $status;
  }
}
