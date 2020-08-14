<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\StudentClass;
use App\Models\UserEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use MacsiDigital\Zoom\Facades\Zoom;
use Yajra\DataTables\DataTables;

class MeetingController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index()
  {
    $title = 'Daftar Meeting';
    return view('backend.meeting.index', compact('title'));
  }

  /**
   * Show data in datatable.
   *
   */
  public function datatable()
  {
    $data = Meeting::orderBy('id', 'desc')->get();
    return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('class_name', function ($query) {
        return $query->studentClass->class_name;
      })
      ->addColumn('action', function ($query) {
        return '<a href="#" class="btn btn-success btn-sm btn-edit" title="Edit Data" id="'. $query->id .'" onclick="regenerateMeeting('. $query->class_id .')"><i class="icon icon-pencil-square-o"></i></a>';;
      })
      ->make(true);
  }

  /**
   * regenerate meeting
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function regenerateMeeting(Request $request)
  {
    $classId = $request->class_id;
    $class = StudentClass::where('id', $classId)->first();
    $user = UserEmployee::where('employee_id', $class->employee_id)->first();
    $userMeeting = null;

    try {
      /* create meeting to zoom */
      $meeting = Zoom::meeting()->make([
        'topic' => $class->class_name,
        'type' => 1,
        'timezone' => 'Asia/Jakarta',
        'password' => Str::random(6)
      ]);

      /* save meeting by user */
      $zoomMeeting = Zoom::user()->find($user->user_id_zoom)->meetings()->save($meeting);

      /* save to table meeting */
      Meeting::updateOrCreate(['class_id' => $classId], [
        'class_id' => $classId,
        'url' => $zoomMeeting->join_url,
        'meeting_id' => $zoomMeeting->id,
        'password' => $zoomMeeting->password
      ]);

      if ($zoomMeeting) {
        $json = ['status' => 200, 'message' => 'Data berhasil digenerate'];
      } else {
        $json = ['status' => 500, 'message' => 'Data gagal digenerate'];
      }
      return response()->json($json);
    } catch (\Exception $e) {
      return response()->json(['status' => 500, 'message' => 'Terjadi kesalahan pada server']);
    }
  }
}
