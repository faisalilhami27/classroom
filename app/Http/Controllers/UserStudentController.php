<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStudentRequest;
use App\Models\Configuration;
use App\Models\UserStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class UserStudentController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index()
  {
    $config = (optional(configuration())->type_school == 1) ? 'Mahasiswa' : 'Siswa';
    $title = 'Daftar User ' . $config;
    return view('backend.userStudent.index', compact('title'));
  }

  /**
   * Show data in datatable.
   *
   */
  public function datatable()
  {
    $data = UserStudent::with('student')
      ->orderBy('id', 'desc')
      ->get();

    return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('status', function ($query) {
        $status = null;

        if ($query->status == 1) {
          $status = '<span class="label label-primary">Aktif</span>';
        } else {
          $status = '<span class="label label-danger">Tidak Aktif</span>';
        }

        return $status;
      })
      ->addColumn('email_verified', function ($query) {
        $emailVerified = null;

        if ($query->status == 1) {
          $emailVerified = '<span class="label label-info">Sudah terverifikasi</span>';
        } else {
          $emailVerified = '<span class="label label-danger">Belum terverifikasi</span>';
        }

        return $emailVerified;
      })
      ->addColumn('action', function ($query) {
        $updateDelete = checkPermission()->update_delete;
        $update = checkPermission()->update;
        $delete = checkPermission()->delete;
        $button = null;

        if ($updateDelete) {
          $button = '<a href="#" class="btn btn-info btn-sm btn-reset" title="Reset Password" id="'. $query->id .'" onclick="resetPassword('. $query->id .')"><i class="icon icon-refresh"></i></a>
                     <a href="#" class="btn btn-success btn-sm btn-edit" title="Edit Data" id="${data.id}" onclick="editData('. $query->id .')"><i class="icon icon-pencil-square-o"></i></a>
                     <a href="#" class="btn btn-danger btn-sm" id="'. $query->id .'" onclick="deleteData('. $query->id .')" title="Delete Data"><i class="icon icon-trash-o"></i></a>';
        } else if ($update) {
          $button = '<a href="#" class="btn btn-info btn-sm btn-reset" title="Reset Password" id="'. $query->id .'" onclick="resetPassword('. $query->id .')"><i class="icon icon-refresh"></i></a>
                     <a href="#" class="btn btn-success btn-sm btn-edit" title="Edit Data" id="${data.id}" onclick="editData('. $query->id .')"><i class="icon icon-pencil-square-o"></i></a>';
        } else if ($delete) {
          $button = '<a href="#" class="btn btn-info btn-sm btn-reset" title="Reset Password" id="'. $query->id .'" onclick="resetPassword('. $query->id .')"><i class="icon icon-refresh"></i></a>
                     <a href="#" class="btn btn-danger btn-sm" id="'. $query->id .'" onclick="deleteData('. $query->id .')" title="Delete Data"><i class="icon icon-trash-o"></i></a>';
        } else {
          $button = '<a href="#" class="btn btn-info btn-sm btn-reset" title="Reset Password" id="'. $query->id .'" onclick="resetPassword('. $query->id .')"><i class="icon icon-refresh"></i></a>';
        }
        return $button;
      })
      ->rawColumns(['status', 'action', 'email_verified'])
      ->make(true);
  }

  /**
   * reset password
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function resetPassword(Request $request)
  {
    $password = Configuration::first()->reset_password_student;

    if (is_null($password)) {
      return response()->json(['status' => 500, 'message' => 'Silahkan isi reset password di konfigurasi']);
    }

    $id = $request->id;
    $data = UserStudent::where('id', $id)->update(['password' => Hash::make($password)]);

    if ($data) {
      $json = ['status' => 200, 'message' => 'Password berhasil reset'];
    } else {
      $json = ['status' => 500, 'message' => 'Password gagal reset'];
    }

    return response()->json($json);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function edit(Request $request)
  {
    $id = $request->id;
    $userStudent = UserStudent::find($id);

    if ($userStudent) {
      $json = ['status' => 200, 'data' => $userStudent];
    } else {
      $json = ['status' => 500, 'message' => 'Data tidak ditemukan'];
    }

    return response()->json($json);
  }

  /**
   * Update the specified resource in storage.
   * @param UserStudentRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(UserStudentRequest $request)
  {
    $id = $request->id;
    $status = $request->status;
    $username = $request->username;
    $emailVerified = $request->email_verified;

    $update = UserStudent::find($id)->update([
      'username' => $username,
      'status' => $status,
      'email_verified' => $emailVerified
    ]);

    if ($update) {
      $json = ['status' => 200, 'message' => 'Data berhasil diubah'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal diubah'];
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
    $delete = UserStudent::find($id)->delete();

    if ($delete) {
      $json = ['status' => 200, 'message' => 'Data berhasil dihapus'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal dihapus'];
    }

    return response()->json($json);
  }
}
