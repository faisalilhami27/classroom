<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\ReceiverAnnouncement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AnnouncementController extends Controller
{
  /**
   * get announcement by user
   */
  public function getAnnouncement()
  {
    $role = Session::get('role_id');
    $today = Carbon::now()->format('Y-m-d');
    $user = Auth::user();

    /* check role teacher or student */
    if ($role == 3) {
      $announcements = Announcement::with(['student', 'studentClass.subject'])
        ->whereHas('receiverAnnouncement', function ($query) use ($user) {
          $query->where('employee_id', $user->employee_id);
        })
        ->orderBy('id', 'desc')
        ->whereDate('end_date', '>=', $today)
        ->get();

      $countAnnouncement = Announcement::whereHas('receiverAnnouncement', function ($query) use ($user) {
        $query->where('employee_id', $user->employee_id);
        $query->where('status_read', 1);
      })
        ->whereDate('end_date', '>=', $today)
        ->count();
    } else {
      $announcements = Announcement::with(['employee', 'studentClass.subject'])
        ->whereHas('receiverAnnouncement', function ($query) use ($user) {
          $query->where('student_id', $user->student_id);
        })
        ->orderBy('id', 'desc')
        ->whereDate('end_date', '>=', $today)
        ->get();

      $countAnnouncement = Announcement::whereHas('receiverAnnouncement', function ($query) use ($user) {
        $query->where('student_id', $user->student_id);
        $query->where('status_read', 1);
      })
        ->whereDate('end_date', '>=', $today)
        ->count();
    }

    return response()->json(['status' => 200, 'data' => $announcements, 'count' => $countAnnouncement]);
  }

  /**
   * update status
   * @return \Illuminate\Http\JsonResponse
   */
  public function updateStatus()
  {
    if (Auth::guard('employee')->check()) {
      $where = ['employee_id' => Auth::user()->employee_id];
    } else {
      $where = ['student_id' => Auth::user()->student_id];
    }

    ReceiverAnnouncement::where($where)->update(['status_read' => 2]);

    return response()->json(['status' => 200, 'count' => 0]);
  }
}
