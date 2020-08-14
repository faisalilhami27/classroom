<?php

namespace App\Http\Controllers;

use App\Http\Requests\FillStudentScoreRequest;
use App\Http\Requests\SendStudentTaskRequest;
use App\Http\Requests\TaskRequest;
use App\Models\Announcement;
use App\Models\Employee;
use App\Models\Posting;
use App\Models\StudentClass;
use App\Models\StudentClassTransaction;
use App\Models\StudentTask;
use App\Models\Task;
use App\Models\TaskFile;
use App\Models\UserStudent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class TaskController extends Controller
{
  /**
   * get student by class
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getStudentByClass(Request $request)
  {
    $classId = $request->class_id;
    $students = StudentClassTransaction::where('class_id', $classId)->get();
    $arrayStudent = [];

    foreach ($students as $student) {
      $arrayStudent[] = [
        'id' => $student->student_id,
        'name' => $student->student->student_identity_number . ' - ' . $student->student->name
      ];
    }

    if ($students) {
      return response()->json(['status' => 200, 'data' => $arrayStudent]);
    } else {
      return response()->json(['status' => 500, 'message' => 'Data tidak ditemukan']);
    }
  }

  /**
   * get task by class
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getTaskByClass(Request $request)
  {
    $classId = $request->class_id;
    if (Auth::guard('student')->check()) {
      $tasks = Task::where('class_id', $classId)
        ->whereHas('studentTasks', function ($query) {
          $query->where('student_id', Auth::user()->student_id);
        })
        ->orderBy('id', 'desc')
        ->paginate(5);
    } else {
      $tasks = Task::where('class_id', $classId)
        ->orderBy('id', 'desc')
        ->paginate(5);
    }
    return response()->json(['status' => 200, 'data' => $tasks]);
  }

  /**
   * get student by task
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getAllStudentByTask(Request $request)
  {
    $taskId = $request->task_id;
    $task = Task::where('id', $taskId)->first();
    $students = StudentTask::where('task_id', $taskId)->get();
    $arrayStudent = [];

    foreach ($students as $student) {
      $arrayStudent[] = [
        'id' => $student->student_id,
        'photo' => (is_null($student->student->photo)) ? null : asset('storage/' . $student->student->photo),
        'name' => $student->student->name,
        'sin_name' => $student->student->student_identity_number . ' - ' . $student->student->name,
        'score' => ($student->score == 0) ? '' : $student->score
      ];
    }

    if ($students) {
      return response()->json(['status' => 200, 'data' => $arrayStudent, 'task' => $task]);
    } else {
      return response()->json(['status' => 500, 'message' => 'Data siswa tidak ditemukan']);
    }
  }

  /**
   * get data task by status
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getDataByStatus(Request $request)
  {
    $taskId = $request->task_id;
    $status = $request->status;
    $countSubmitStatus = StudentTask::where('status', 2)
      ->where('task_id', $taskId)
      ->count();
    $countLateStatus = StudentTask::where('status', 1)
      ->where('task_id', $taskId)
      ->count();
    $countUnSubmitStatus = StudentTask::where('status', 3)
      ->where('task_id', $taskId)
      ->count();
    $countUnAllStatus = StudentTask::where('task_id', $taskId)
      ->count();
    $tasks = StudentTask::with('student')
      ->where('task_id', $taskId);

    if ($status != 'all') {
      $tasks->where('status', $status);
    }

    $studentTasks = $tasks->get();
    $data = [
      'count_submit' => $countSubmitStatus,
      'count_late' => $countLateStatus,
      'count_unSubmit' => $countUnSubmitStatus,
      'count_all' => $countUnAllStatus,
      'student_tasks' => $studentTasks
    ];
    return response()->json(['status' => 200, 'data' => $data]);
  }

  /**
   * edit data task
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function edit(Request $request)
  {
    $id = $request->id;
    $task = Task::with(['taskFiles', 'studentTasks'])
      ->where('id', $id)
      ->first();
    $images = [];

    /* looping images */
    foreach ($task->taskFiles as $taskFile) {
      $images[] = [
        'url' => asset('storage/' . $taskFile->file),
        'filename' => $taskFile->filename,
        'mime' => $taskFile->mime_type
      ];
    }

    if ($task) {
      $json = ['status' => 200, 'data' => $task, 'images' => $images];
    } else {
      $json = ['status' => 500, 'message' => 'Data tidak ditemukan'];
    }
    return response()->json($json);
  }

  /**
   * store task
   * @param TaskRequest $request
   * @param Task $params
   * @return \Illuminate\Http\JsonResponse
   * @throws \Pusher\PusherException
   */
  public function store(TaskRequest $request, Task $params)
  {
    $title = htmlspecialchars($request->title, true);
    $instruction = htmlspecialchars($request->instruction, true);
    $point = htmlspecialchars($request->point, true);
    $date = $request->date;
    $time = $request->time;
    $files = $request->file('file');
    $students = $request->students;
    $classId = $request->class_id;
    $class = StudentClass::where('id', $classId)->first();
    $task = Task::orderBy('id', 'desc')->first();

    /* insert data to posting table */
    $posting = Posting::create([
      'title' => htmlspecialchars($title, true),
      'date' => Carbon::now()->format('Y-m-d H:i:s'),
      'type_post' => 2,
      'class_id' => $classId,
      'employee_id' => Auth::user()->employee_id
    ]);

    $task = Task::create([
      'title' => $title,
      'date' => Carbon::now()->format('Y-m-d H:i:s'),
      'deadline_date' => $date,
      'time' => $time,
      'max_score' => $point,
      'task_to' => (is_null($task)) ? 1 : $task->task_to + 1,
      'show_score' => 1,
      'description' => $instruction,
      'class_id' => $classId,
      'employee_id' => Auth::user()->employee_id,
      'posting_id' => $posting->id
    ]);

    /* store student */
    $this->storeStudent($students, $task->id);

    /* filled if the file not null */
    if (!is_null($files)) {
      $this->storeImages($files, $class, $task->id);
    }

    /* create announcement for send to students  */
    $receivers = $this->createAnnouncement($title, $students, $params, $classId);

    /* call pusher configuration for push notification */
    $params->pusherConfig($params->getUsername($receivers));

    if ($task) {
      $json = ['status' => 200, 'message' => 'Data berhasil disimpan'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal disimpan'];
    }
    return response()->json($json);
  }

  /**
   * create announcement
   * @param $title
   * @param $students
   * @param Task $task
   * @param $classId
   * @return array
   */
  private function createAnnouncement($title, $students, $task, $classId)
  {
    $user = Auth::user();
    $studentLists = json_decode($students);
    $data = [];
    $announcement = null;

    $employee = Employee::where('id', $user->employee_id)->first();
    $announcement = Announcement::create([
      'class_id' => $classId,
      'title' => $employee->name . ' Membuat tugas baru ' . $title,
      'type' => 2,
      'created_by_employee' => $user->employee_id,
      'end_date' => Carbon::now()->addDays(1)
    ]);

    /* loop student by class */
    foreach ($studentLists as $list) {
      $data[] = [
        'announcement_id' => $announcement->id,
        'student_id' => $list,
        'status_read' => 1,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ];
    }

    $announcement->receiverAnnouncement()->insert($data);

    /* send announcement to student email */
    $task->sendMail($studentLists, $announcement);
    return $data;
  }

  /**
   * update data task
   * @param TaskRequest $request
   * @param Task $params
   * @return \Illuminate\Http\JsonResponse
   * @throws \Pusher\PusherException
   */
  public function update(TaskRequest $request, Task $params)
  {
    $id = $request->id;
    $title = htmlspecialchars($request->title, true);
    $instruction = htmlspecialchars($request->instruction, true);
    $point = htmlspecialchars($request->point, true);
    $date = $request->date;
    $time = $request->time;
    $files = $request->file('file');
    $students = $request->students;
    $classId = $request->class_id;
    $class = StudentClass::where('id', $classId)->first();

    $task = Task::find($id);

    /* update data task */
    $task->update([
      'title' => $title,
      'deadline_date' => $date,
      'time' => $time,
      'max_score' => $point,
      'description' => $instruction
    ]);

    /* update data to posting table */
    $task->posting()->update(['title' => $title]);

    /* delete data old student whose status is 3 or un submit task */
    if (!is_null($task->studentTasks())) {
      $task->studentTasks()->where('status', 3)
        ->where('task_id', $id)
        ->delete();
    }

    /* store student */
    $this->storeStudent($students, $task->id);

    /* delete old files */
    if (!is_null($task->taskFiles())) {
      foreach ($task->taskFiles()->get() as $item) {
        Storage::disk('public')->delete($item->file);
        $task->taskFiles()->where('id', $item->id)->delete();
      }
    }

    /* filled if the file not null */
    if (!is_null($files)) {
      $this->storeImages($files, $class, $task->id);
    }

    /* call pusher configuration for push notification */
    $params->pusherConfig();

    if ($task) {
      $json = ['status' => 200, 'message' => 'Data berhasil dihapus'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal dihapus'];
    }
    return response()->json($json);
  }

  /**
   * delete data task
   * @param Request $request
   * @param Task $params
   * @return \Illuminate\Http\JsonResponse
   * @throws \Pusher\PusherException
   */
  public function destroy(Request $request, Task $params)
  {
    $id = $request->id;
    $task = Task::find($id);

    $task->posting()->delete();

    /* delete old files */
    if (!is_null($task->taskFiles())) {
      foreach ($task->taskFiles()->get() as $post) {
        Storage::disk('public')->delete($post->file);
      }
      $task->taskFiles()->where('task_id', $id)->delete();
    }

    /* delete old student data  */
    if (!is_null($task->studentTasks())) {
      $task->studentTasks()->where('task_id', $id)->delete();
    }

    $task->delete();

    /* call pusher configuration for push notification */
    $params->pusherConfig();

    if ($task) {
      $json = ['status' => 200, 'message' => 'Data berhasil dihapus'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal dihapus'];
    }
    return response()->json($json);
  }

  /**
   * store images
   * @param $files
   * @param $class
   * @param $id
   */
  private function storeImages($files, $class, $id)
  {
    $image = [];
    $className = str_replace('/', '-', $class->className);
    foreach ($files as $file) {
      $filename = $file->getClientOriginalName();
      $replaceFileName = pathinfo($filename, PATHINFO_FILENAME) . '_' . time() . '.' . $file->getClientOriginalExtension();
      $image[] = [
        'task_id' => $id,
        'file' => $file->storeAs('task/' . $className, $replaceFileName),
        'filename' => $file->getClientOriginalName(),
        'mime_type' => $file->getClientMimeType(),
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ];
    }
    TaskFile::insert($image);
  }

  /**
   * store students
   * @param $students
   * @param $id
   */
  private function storeStudent($students, $id)
  {
    $decode = json_decode($students);
    foreach ($decode as $student) {
      StudentTask::updateOrCreate(
        [
          'student_id' => $student,
          'task_id' => $id
        ],
        [
          'task_id' => $id,
          'student_id' => $student,
          'status' => 3,
          'score' => 0,
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }
  }

  /**
   * get student task
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getStudentTask(Request $request)
  {
    $taskId = $request->task_id;
    $studentId = Auth::user()->student_id;
    $task = StudentTask::with('task')
      ->where('task_id', $taskId)
      ->where('student_id', $studentId)
      ->first();

    $image = [
      'url' => asset('storage/' . optional($task)->task_file),
      'filename' => optional($task)->filename,
      'mime' => optional($task)->mime_type
    ];
    return response()->json(['status' => 200, 'data' => $task, 'image' => $image]);
  }

  /**
   * send student task
   * @param SendStudentTaskRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function sendStudentTask(SendStudentTaskRequest $request)
  {
    $taskId = $request->task_id;
    $studentId = Auth::user()->student_id;
    $file = $request->file('file');
    $filename = $file->getClientOriginalName();
    $replaceFileName = pathinfo($filename, PATHINFO_FILENAME) . '_' . time() . '.' . $file->getClientOriginalExtension();
    $task = Task::where('id', $taskId)->first();
    $class = StudentClass::where('id', $task->class_id)->first();
    $className = str_replace('/', '-', $class->className);
    $today = Carbon::now()->format('Y-m-d H:i:s');
    $deadlineDate = $task->deadline_date . ' ' . $task->time;
    $studentTask = StudentTask::where('student_id', $studentId)
      ->where('task_id', $taskId)
      ->first();

    $studentTask->update([
      'task_file' => $file->storeAs('task/' . $className . '/student_task/task_' . $task->task_to, $replaceFileName),
      'filename' => $filename,
      'mime_type' => $file->getClientMimeType(),
      'status' => ($deadlineDate > $today) ? 2 : 1
    ]);

    if ($studentTask) {
      $json = ['status' => 200, 'message' => 'Data berhasil disimpan'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal disimpan'];
    }
    return response()->json($json);
  }

  /**
   * cancel send task
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function cancelSendTask(Request $request)
  {
    $taskId = $request->task_id;
    $studentId = Auth::user()->student_id;
    $studentTask = StudentTask::where('task_id', $taskId)
      ->where('student_id', $studentId)
      ->first();

    /* delete old file */
    Storage::disk('public')->delete($studentTask->task_file);

    $cancel = $studentTask->update([
      'task_file' => null,
      'filename' => null,
      'mime_type' => null,
      'status' => 3
    ]);;
    if ($cancel) {
      $json = ['status' => 200, 'message' => 'Data berhasil dibatalkan'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal dibatalkan'];
    }
    return response()->json($json);
  }

  /**
   * download all task by class to zip file
   * @param $taskId
   * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
   */
  public function downloadTaskFile($taskId)
  {
    $headers = ['Content-Type' => 'application/octet-stream'];
    $task = Task::where('id', $taskId)->first();
    $className = str_replace('/', '-', $task->studentClass->className);
    $zipFile = $className . '.zip';
    $path = public_path('storage/task/' . $className . '/student_task/task_' . $task->task_to . '/' . $zipFile);
    $zip = new ZipArchive;
    if ($zip->open($path, ZipArchive::CREATE) === TRUE) {
      $filePath = 'storage/task/' . $className . '/student_task/task_' . $task->task_to;
      if (File::exists($filePath)) {
        $files = File::allFiles($filePath);
        if (count($files) > 0) {
          foreach ($files as $key => $value) {
            $relativeNameInZipFile = basename($value);
            $zip->addFile($value, $relativeNameInZipFile);
          }
          $zip->close();
          return response()->download($path, $zipFile, $headers)->deleteFileAfterSend(true);
        } else {
          return response()->json(['status' => 500, 'message' => 'Belum ada yang mengumpulkan tugas']);
        }
      } else {
        return response()->json(['status' => 500, 'message' => 'File tidak ditemukan']);
      }
    }
  }

  /**
   * change status shows score whether will be displayed or not
   * @param Request $request
   * @param Task $params
   * @return \Illuminate\Http\JsonResponse
   * @throws \Pusher\PusherException
   */
  public function showScore(Request $request, Task $params)
  {
    $taskId = $request->task_id;
    $status = ($request->status) ? 2 : 1;
    $task = Task::find($taskId);
    $update = $task->update(['show_score' => $status]);

    if ($update) {
      $json = ['status' => 200, 'data' => $task, 'message' => 'Data berhasil disimpan'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal diubah'];
    }

    /* call pusher configuration for push notification */
    $params->pusherConfig($task->show_score);

    return response()->json($json);
  }

  /**
   * fill in student scores
   * @param FillStudentScoreRequest $request
   * @param Task $params
   * @return \Illuminate\Http\JsonResponse
   * @throws \Pusher\PusherException
   */
  public function fillStudentScore(FillStudentScoreRequest $request, Task $params)
  {
    $taskId = $request->task_id;
    $studentId = $request->student_id;
    $score = $request->score;
    $user = UserStudent::where('student_id', $studentId)->first();
    $task = StudentTask::where('task_id', $taskId)
      ->where('student_id', $studentId)
      ->first();
    $update = $task->update(['score' => $score]);

    if ($update) {
      $json = ['status' => 200, 'message' => 'Nilai berhasil disimpan'];
    } else {
      $json = ['status' => 500, 'message' => 'Nilai gagal disimpan'];
    }

    /* call pusher configuration for push notification */
    $data = [
      'score' => $task->score,
      'username' => $user->username
    ];
    $params->pusherConfig($data);

    return response()->json($json);
  }
}
