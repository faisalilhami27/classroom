<?php

namespace App\Http\Controllers;

use App\Http\Requests\VideoConferenceRequest;
use App\Models\Announcement;
use App\Models\Employee;
use App\Models\Meeting;
use App\Models\StudentClass;
use App\Models\StudentClassTransaction;
use App\Models\UserEmployee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use MacsiDigital\Zoom\Facades\Zoom;
use Pusher\Pusher;

class VideoConferenceController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index()
  {
    $title = 'Generate User Zoom';
    $user = Employee::where('id', Auth::user()->employee_id)->first();
    $zoomAccount = Zoom::user()->find(optional($user->userEmployee)->user_id_zoom);
    return view('backend.zoomAccount.generate', compact('title', 'user', 'zoomAccount'));
  }

  /**
   * generate user zoom
   * @param VideoConferenceRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function generate(VideoConferenceRequest $request)
  {
    $id = $request->id;
    $firstName = htmlspecialchars($request->first_name);
    $lastName = htmlspecialchars($request->last_name);
    $email = htmlspecialchars($request->email);
    $employee = Employee::find($id);

    try {
      /* update data to the employee table */
      $employee->update([
        'name' => $firstName . ' ' . $lastName,
        'first_name' => $firstName,
        'last_name' => ($lastName == '-') ? null : $lastName,
        'email' => $email
      ]);

      /* insert data user to zoom */
      $insert = Zoom::user()->create([
        'first_name' => $firstName,
        'last_name' => ($lastName == '-') ? '' : $lastName,
        'email' => $email
      ]);

      /* update data to the user employee table */
      $employee->userEmployee()->update([
        'status_generate' => 1,
        'user_id_zoom' => $insert->id
      ]);

      if ($insert) {
        $json = ['status' => 200, 'message' => 'Data berhasil digenerate'];
      } else {
        $json = ['status' => 500, 'message' => 'Data gagal digenerate'];
      }
      return response()->json($json);
    } catch (\Exception $e) {
      return response()->json(['status' => 500, 'message' => 'Terjadi kesalahan pada server']);
    }
  }

  /**
   * get meeting by class
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getMeeting(Request $request)
  {
    $classId = $request->class_id;
    $meeting = Meeting::where('class_id', $classId)->first();

    $json = ['status' => 200, 'data' => $meeting];
    return response()->json($json);
  }

  /**
   * create meeting by user
   * @param Request $request
   * @param Meeting $meet
   * @return \Illuminate\Http\JsonResponse
   */
  public function createMeeting(Request $request, Meeting $meet)
  {
    $classId = $request->class_id;
    $userId = Auth::user()->employee_id;
    $class = StudentClass::where('id', $classId)->first();
    $user = UserEmployee::where('employee_id', $userId)->first();
    $meetingByClass = Meeting::where('class_id', $classId);
    $userMeeting = null;
    $receivers = null;

    try {
      if (is_null($user->user_id_zoom)) {
       return response()->json([
         'status' => 500,
         'message' => 'Silahkan generate akun zoom terlebih dahulu di halaman admin'
       ]);
      }

      /* create meeting to zoom */
      $meeting = Zoom::meeting()->make([
        'topic' => $class->class_name,
        'type' => 1,
        'timezone' => 'Asia/Jakarta',
        'password' => Str::random(6)
      ]);

      /* save meeting by user */
      $zoomMeeting = Zoom::user()->find($user->user_id_zoom)->meetings()->save($meeting);

      if (is_null($meetingByClass->first())) {
        /* create announcement for send to students  */
        $receivers = $this->createAnnouncement($classId);
      }

      /* save to table meeting */
      $insert = $meetingByClass->updateOrCreate(['class_id' => $classId], [
        'class_id' => $classId,
        'url' => $zoomMeeting->join_url,
        'meeting_id' => $zoomMeeting->id,
        'password' => $zoomMeeting->password
      ]);

      /* call pusher configuration for push notification */
      $meet->pusherConfig($meet->getUsername($receivers));

      if ($zoomMeeting) {
        $json = ['status' => 200, 'data' => $insert];
      } else {
        $json = ['status' => 500, 'message' => 'Gagal membuat meeting'];
      }
      return response()->json($json);
    } catch (\Exception $e) {
      return response()->json(['status' => 500, 'message' => 'Terjadi kesalahan pada server']);
    }
  }

  /**
   * create announcement
   * @param $classId
   * @return array
   */
  private function createAnnouncement($classId)
  {
    $user = Auth::user();
    $studentClassTransactions = StudentClassTransaction::where('class_id', $classId)->get();
    $data = [];
    $announcement = null;

    $employee = Employee::where('id', $user->employee_id)->first();
    $announcement = Announcement::create([
      'class_id' => $classId,
      'title' => $employee->name . ' Membuat link meeting',
      'type' => 1,
      'created_by_employee' => $user->employee_id,
      'end_date' => Carbon::now()->addDays(1)
    ]);

    /* loop student by class */
    foreach ($studentClassTransactions as $studentClassTransaction) {
      $data[] = [
        'announcement_id' => $announcement->id,
        'student_id' => $studentClassTransaction->student_id,
        'status_read' => 1,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
      ];
    }

    $announcement->receiverAnnouncement()->insert($data);
    return $data;
  }

  /**
   * delete meeting if done
   * @param Request $request
   * @param Meeting $meet
   * @return \Illuminate\Http\JsonResponse
   * @throws \Pusher\PusherException
   */
  public function deleteMeeting(Request $request, Meeting $meet)
  {
    $classId = $request->class_id;
    $meeting = Meeting::where('class_id', $classId)->delete();
    $meet->pusherConfig();
    if ($meeting) {
      $json = ['status' => 200, 'message' => 'Pembelajaran online telah selesai'];
    } else {
      $json = ['status' => 500, 'message' => 'Terjadi kesalahan pada server'];
    }
    return response()->json($json);
  }
}
