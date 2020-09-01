<?php

namespace App\Http\Controllers;

use App\Exports\TaskScoreStudentExport;
use App\Models\ManageExam;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class AssessmentController extends Controller
{
  /**
   * get task assessment
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getTaskAssessment(Request $request)
  {
    $classId = $request->class_id;
    $tasks = Task::where('class_id', $classId)->paginate(5);
    return response()->json([
      'status' => 200,
      'task' => $tasks
    ]);
  }

  /**
   * get exam assessment
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getExamAssessment(Request $request)
  {
    $subjectName = subjectName();
    $level = level();
    $classId = $request->class_id;
    $exams = ManageExam::with(['subject', 'semester', 'gradeLevel'])
      ->whereHas('examClass', function ($query) use ($classId) {
        $query->where('class_id', $classId);
      })
      ->orderBy('id', 'desc')
      ->paginate(5);
    return response()->json([
      'status' => 200,
      'data' => $exams,
      'subject_name' => $subjectName,
      'level' => $level
      ]);
  }

  /**
   * export student score to excel
   * @param $taskId
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   */
  public function taskExport($taskId)
  {
    $task = Task::with([
        'studentClass',
        'studentClass.gradeLevel',
        'studentClass.semester',
      ])
      ->where('id', $taskId)
      ->first();
    $fileName = 'Tugas ke-' . $task->task_to . ' ' . $this->className($task);
    return Excel::download(new TaskScoreStudentExport($taskId), $fileName . '.xlsx');
  }

  /**
   * class name
   * @param $task
   * @return string
   */
  private function className($task)
  {
    $config = optional(configuration())->type_school;
    $name = null;

    if ($config == 1) {
      $name = $task->studentClass->subject->name . " Kelas " .
        $task->studentClass->class_order;
    } else if ($config == 2) {
      $name = $task->studentClass->subject->name . " Kelas " .
        $task->studentClass->class_order . " - " .
        $task->studentClass->gradeLevel->name . " - " .
        $task->studentClass->major->code . " - " .
        $task->studentClass->class_order;
    } else {
      $name = $task->studentClass->subject->name . " Kelas " .
        $task->studentClass->class_order . " - " .
        $task->studentClass->gradeLevel->name . " - " .
        $task->studentClass->class_order;
    }
    return $name;
  }
}
