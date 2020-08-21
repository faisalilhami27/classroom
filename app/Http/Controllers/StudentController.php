<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;
use App\Models\Student;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class StudentController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index()
  {
    $title = 'Daftar Siswa';
    return view('backend.student.index', compact('title'));
  }

  /**
   * get data employee from database
   */
  public function getDataStudent()
  {
    $data = Student::orderBy('id', 'desc')->get();

    return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('action', function ($query) {
        $updateDelete = checkPermission()->update_delete;
        $update = checkPermission()->update;
        $delete = checkPermission()->delete;
        $button = null;

        if ($updateDelete) {
          $button = '<a href="#" class="btn btn-success btn-sm btn-edit" title="Edit Data" id="'. $query->id .'" onclick="editData('. $query->id .')"><i class="icon icon-pencil-square-o"></i></a>
                     <a href="#" class="btn btn-danger btn-sm" id="'. $query->id .'" onclick="deleteData('. $query->id .')" title="Delete Data"><i class="icon icon-trash-o"></i></a>';
        } else if ($update) {
          $button = '<a href="#" class="btn btn-success btn-sm btn-edit" title="Edit Data" id="'. $query->id .'" onclick="editData('. $query->id .')"><i class="icon icon-pencil-square-o"></i></a>';
        } else if ($delete) {
          $button = '<a href="#" class="btn btn-danger btn-sm" id="'. $query->id .'" onclick="deleteData('. $query->id .')" title="Delete Data"><i class="icon icon-trash-o"></i></a>';
        } else {
          $button = 'Tidak ada aksi';
        }
        return $button;
      })->make(true);
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
    $data = Student::where('id', $id)->first();

    if ($data) {
      $json = ['status' => 200, 'data' => $data];
    } else {
      $json = ['status' => 500, 'message' => 'Data tidak ditemukan'];
    }

    return response()->json($json);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(StudentRequest $request)
  {
    $data = $request->all();
    $id = $request->id;
    $update = Student::where('id', $id)->first()->update($data);

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
    $delete = Student::where('id', $id)->first()->delete();

    if ($delete) {
      $json = ['status' => 200, 'message' => 'Data berhasil dihapus'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal dihapus'];
    }

    return response()->json($json);
  }

  /**
   * check duplicate email
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function checkEmail(Request $request)
  {
    $email = $request->email;
    $data = Student::where('email', $email)->first();

    if (is_null($data)) {
      $json = ['status' => 200, 'message' => 'Email tersedia'];
    } else {
      $json = ['status' => 500, 'message' => 'Email sudah digunakan'];
    }

    return response()->json($json);
  }

  /**
   * check duplicate phone number
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function checkPhoneNumber(Request $request)
  {
    $phoneNumber = $request->phone_number;
    $data = Student::where('phone_number', $phoneNumber)->first();

    if (is_null($data)) {
      $json = ['status' => 200, 'message' => 'Nomor Handphone tersedia'];
    } else {
      $json = ['status' => 500, 'message' => 'Nomor Handphone sudah digunakan'];
    }

    return response()->json($json);
  }
}
