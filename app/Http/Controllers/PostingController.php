<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostingRequest;
use App\Models\Announcement;
use App\Models\Employee;
use App\Models\Posting;
use App\Models\PostingImage;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\StudentClassTransaction;
use App\Models\Task;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostingController extends Controller
{
  /**
   * get data posting by class
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getDataPosting(Request $request)
  {
    $classId = $request->class_id;
    if (Auth::guard('student')->check()) {
      $data = Posting::with(['getImages', 'student.userStudent', 'employee.userEmployee'])
        ->with(['task.studentTask' => function ($query) {
          $query->where('student_id', Auth::user()->student_id);
        }])
        ->where('class_id', $classId)
        ->orderBy('id', 'desc');

      $postings = $data->paginate(5);
    } else {
      $postings = Posting::with(['getImages', 'student.userStudent', 'employee.userEmployee', 'task'])
        ->where('class_id', $classId)
        ->orderBy('id', 'desc')
        ->paginate(5);
    }

    if ($postings) {
      $json = ['status' => 200, 'data' => $postings];
    } else {
      $json = ['status' => 500, 'message' => 'Data tidak ditemukan'];
    }
    return response()->json($json);
  }

  /**
   * get data posting by id
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(Request $request)
  {
    $id = $request->id;
    $posting = Posting::where('id', $id)->first();

    if (is_null($posting)) {
      return response()->json(['message' => 'Data tidak ditemukan'], 500);
    }

    $data = [
      'id' => $posting->id,
      'maker' => (!is_null($posting->employee_id)) ? $posting->employee->name : $posting->student->name,
      'title' => $posting->title,
      'date' => date('d', strtotime($posting->date)) . ' ' . convertMonthName(date('m', strtotime($posting->date))) . ' ' . date('Y', strtotime($posting->date)),
      'task' => [
        'deadline' => date('d', strtotime($posting->date)) . ' ' . convertMonthName(date('m', strtotime($posting->date))) . ' ' . date('Y', strtotime($posting->date)) . ' ' . date('H:i', strtotime(optional($posting->task)->time)),
        'instruction' => optional($posting->task)->description,
        'files' => optional($posting->task)->taskFiles
      ]
    ];

    if ($posting) {
      $json = ['status' => 200, 'data' => $data];
    } else {
      $json = ['status' => 500, 'message' => 'Data tidak ditemukan'];
    }
    return response()->json($json);
  }

  /**
   * edit data posting
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function edit(Request $request)
  {
    $id = $request->id;
    $posting = Posting::with(['getImages'])
      ->where('id', $id)
      ->first();
    $images = [];

    /* looping images */
    foreach ($posting->getImages as $getImage) {
      $images[] = [
        'url' => asset('storage/' . $getImage->file),
        'filename' => $getImage->filename,
        'mime' => $getImage->mime_type
      ];
    }

    if ($posting) {
      $json = ['status' => 200, 'data' => $posting, 'images' => $images];
    } else {
      $json = ['status' => 500, 'message' => 'Data tidak ditemukan'];
    }
    return response()->json($json);
  }

  /**
   * store posting
   * @param PostingRequest $request
   * @param Posting $post
   * @return \Illuminate\Http\JsonResponse
   * @throws \Pusher\PusherException
   */
  public function store(PostingRequest $request, Posting $post)
  {
    $classId = $request->class_id;
    $title = $request->title;
    $files = $request->file('file');
    $class = StudentClass::where('id', $classId)->first();

    /* check guard */
    if (Auth::guard('employee')->check()) {
      $employeeId = $request->user_id;
      $studentId = null;
    } else {
      $employeeId = null;
      $studentId = $request->user_id;
    }

    $data = [
      'title' => htmlspecialchars($title, true),
      'date' => Carbon::now()->format('Y-m-d H:i:s'),
      'type_post' => 1,
      'class_id' => $classId,
      'employee_id' => $employeeId,
      'student_id' => $studentId,
    ];

    $posting = Posting::create($data);

    /* create announcement for send to students  */
    $receivers = $this->createAnnouncement($classId, $title, $post);

    /* filled if the file not null */
    if (!is_null($files)) {
      $this->storeImages($files, $class, $posting->id);
    }

    /* call pusher configuration for push notification */
    $post->pusherConfig($post->getUsername($receivers));

    if ($posting) {
      $json = ['status' => 200, 'message' => 'Data berhasil disimpan'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal disimpan'];
    }
    return response()->json($json);
  }

  /**
   * create announcement
   * @param $classId
   * @param $title
   * @param Posting $posting
   * @return
   */
  private function createAnnouncement($classId, $title, $posting)
  {
    $user = Auth::user();
    $studentClassTransactions = StudentClassTransaction::where('class_id', $classId)->get();
    $data = [];
    $announcement = null;

    if (Auth::guard('employee')->check()) {
      $employee = Employee::where('id', $user->employee_id)->first();
      $announcement = Announcement::create([
        'class_id' => $classId,
        'title' => $employee->name . ' Membuat postingan ' . $title,
        'type' => 2,
        'created_by_employee' => $user->employee_id,
        'end_date' => Carbon::now()->addDays(1)
      ]);

      /* loop student by class */
      foreach ($studentClassTransactions as $studentClassTransaction) {
        $data[] = [
          'announcement_id' => $announcement->id,
          'student_id' => $studentClassTransaction->student_id,
          'employee_id' => null,
          'status_read' => 1,
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ];
      }
    } else {
      $class = StudentClass::where('id', $classId)->first();
      $student = Student::where('id', $user->student_id)->first();
      $announcement = Announcement::create([
        'class_id' => $classId,
        'title' => $student->name . ' Membuat postingan ' . $title,
        'type' => 2,
        'created_by_student' => $user->student_id,
        'end_date' => Carbon::now()->addDays(1)
      ]);

      $data[] = [
        'announcement_id' => $announcement->id,
        'employee_id' => $class->employee_id,
        'student_id' => null,
        'status_read' => 1,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ];
    }

    $announcement->receiverAnnouncement()->insert($data);

    /* send announcement to student email */
    $posting->sendMail($classId, $announcement, 'posting');
    return $data;
  }

  /**
   * update posting
   * @param PostingRequest $request
   * @param Posting $post
   * @return \Illuminate\Http\JsonResponse
   * @throws \Pusher\PusherException
   */
  public function update(PostingRequest $request, Posting $post)
  {
    $classId = $request->class_id;
    $id = $request->id;
    $title = $request->title;
    $files = $request->file('file');
    $class = StudentClass::where('id', $classId)->first();

    $posting = Posting::find($id);
    $posting->update([
      'title' => htmlspecialchars($title, true),
    ]);

    /* delete old files */
    if (!is_null($posting->getImages())) {
      foreach ($posting->getImages()->get() as $post) {
        Storage::disk('public')->delete($post->file);
        $posting->getImages()->where('id', $post->id)->delete();
      }
    }

    /* filled if the file not null */
    if (!is_null($files)) {
      $this->storeImages($files, $class, $id);
    }

    /* call pusher configuration for push notification */
    $post->pusherConfig();

    if ($posting) {
      $json = ['status' => 200, 'message' => 'Data berhasil disimpan'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal disimpan'];
    }
    return response()->json($json);
  }

  /**
   * delete data posting
   * @param Request $request
   * @param Posting $post
   * @return \Illuminate\Http\JsonResponse
   * @throws \Pusher\PusherException
   */
  public function destroy(Request $request, Posting $post)
  {
    $id = $request->id;
    $posting = Posting::find($id);

    /* delete old files */
    if (!is_null($posting->getImages())) {
      foreach ($posting->getImages()->get() as $post) {
        Storage::disk('public')->delete($post->file);
      }
      $posting->getImages()->where('posting_id', $id)->delete();
    }

    $posting->forum()->delete();
    $posting->delete();

    /* call pusher configuration for push notification */
    $post->pusherConfig();
    if ($posting) {
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
        'posting_id' => $id,
        'file' => $file->storeAs('posting/' . $className, $replaceFileName),
        'filename' => $file->getClientOriginalName(),
        'mime_type' => $file->getClientMimeType(),
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ];
    }
    PostingImage::insert($image);
  }

  /**
   * get the closest task
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   * @throws \Exception
   */
  public function getClosestTask(Request $request)
  {
    $classId = $request->class_id;
    $today = Carbon::now()->format('Y-m-d');
    $nextDays = Carbon::now()->addDay(1)->format('Y-m-d');
    $sevenDays = Carbon::now()->addDay(8)->format('Y-m-d');
    $begin = new DateTime($today);
    $end = new DateTime($sevenDays);
    $dateRange = new DatePeriod($begin, new DateInterval('P1D'), $end);
    $data = collect([]);

    if (Auth::guard('student')->check()) {
      $taskDeadlineDate = Task::where('class_id', $classId)
        ->whereHas('studentTasks', function ($query) {
          $query->where('student_id', Auth::user()->student_id);
        })
        ->where('deadline_date', '>=', $today)
        ->orderBy('deadline_date', 'asc')
        ->limit(2)
        ->take(2)
        ->get();
    } else {
      $taskDeadlineDate = Task::where('class_id', $classId)
        ->where('deadline_date', '>=', $today)
        ->orderBy('deadline_date', 'asc')
        ->limit(2)
        ->take(2)
        ->get();
    }

    foreach ($dateRange as $date) {
      foreach ($taskDeadlineDate as $task) {
        if ($date->format('Y-m-d') == $task->deadline_date) {
          $deadlineDate = date('N', strtotime($task->deadline_date));
          $data[] = $this->checkClosestDays($today, $task->deadline_date, $task->time, $nextDays, $task->title, $deadlineDate);
        }
      }
    }

    return response()->json(['status' => 200, 'data' => $data]);
  }

  /**
   * convert month to day
   * @param $date
   * @return string
   */
  private function convertMonthToDay($date)
  {
    $string = null;
    switch ($date) {
      case 1:
        $string = 'Senin';
        break;
      case 2:
        $string = 'Selasa';
        break;
      case 3:
        $string = 'Rabu';
        break;
      case 4:
        $string = 'Kamis';
        break;
      case 5:
        $string = 'Jumat';
        break;
      case 6:
        $string = 'Sabtu';
        break;
      case 7:
        $string = 'Minggu';
        break;
      default:
        $string = "Undefined";
        break;
    }
    return $string;
  }

  /**
   * check closest day
   * @param $today
   * @param $deadline
   * @param $time
   * @param $nextDays
   * @param $title
   * @param $deadlineDate
   * @return array|null
   */
  private function checkClosestDays($today, $deadline, $time, $nextDays, $title, $deadlineDate)
  {
    $array = null;
    if ($today == $deadline) {
      $array = [
        'title' => $title,
        'date' => 'Hari ini',
        'time' => date('H:i', strtotime($time))
      ];
    } elseif ($nextDays == $deadline) {
      $array = [
        'title' => $title,
        'date' => 'Besok',
        'time' => date('H:i', strtotime($time))
      ];
    } else {
      $array = [
        'title' => $title,
        'date' => $this->convertMonthToDay($deadlineDate),
        'time' => date('H:i', strtotime($time))
      ];
    }
    return $array;
  }
}
