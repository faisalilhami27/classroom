<?php


namespace App\Exports;


use App\Models\AssignExamStudent;
use App\Models\ManageExam;
use App\Models\StudentExamScore;
use App\Models\StudentTask;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;

class TaskScoreStudentExport implements FromView, WithMultipleSheets
{
  use Exportable;

  private $taskId;

  public function __construct($taskId)
  {
    $this->taskId = $taskId;
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
    $task = Task::with(['studentClass.subject'])
      ->where('id', $this->taskId)
      ->first();
    $studentTasks = StudentTask::with(['student'])
      ->where('task_id', $this->taskId)
      ->get();
    $collection = [];

    foreach ($studentTasks as $studentTask) {
      $collection[] = (object) [
        'sin' => $studentTask->student->student_identity_number,
        'name' => $studentTask->student->name,
        'class' => $task->studentClass->class_name,
        'score' => $studentTask->score
      ];
    }
    $data = collect($collection)->sortBy('name');
    return view('backend.task.excel', compact('data', 'task'));
  }

  public function sheets(): array
  {
    $sheets = [];
    $sheets[] = new TaskScoreStudentExport($this->taskId);
    return $sheets;
  }
}
