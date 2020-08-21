<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeImportRequest;
use App\Http\Requests\EmployeeRequest;
use App\Http\Requests\MajorImportRequest;
use App\Http\Requests\VideoConferenceRequest;
use App\Imports\EmployeeImport;
use App\Models\Employee;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class EmployeeController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index()
  {
    $title = 'Daftar Karyawan';
    return view('backend.employee.index', compact('title'));
  }

  /**
   * import data major from excel
   * @param EmployeeImportRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function import(EmployeeImportRequest $request)
  {
    $file = $request->file('employee_import');

    $import = Excel::import(new EmployeeImport(), $file);

    if ($import) {
      return response()->json(['status' => 200, 'message' => 'Data berhasil diimport']);
    } else {
      return response()->json(['status' => 500, 'message' => 'Data gagal diimport']);
    }
  }

  /**
   * get data employee from database
   */
  public function getDataEmployee()
  {
    $data = Employee::whereNotIn('id', [1,2])
      ->orderBy('id', 'desc')
      ->get();

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
   * Store a newly created resource in storage.
   *
   * @param EmployeeRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(EmployeeRequest $request)
  {
    $firstName = htmlspecialchars($request->first_name);
    $lastName = htmlspecialchars($request->last_name);
    $ein = htmlspecialchars($request->employee_identity_number);
    $phoneNumber = htmlspecialchars($request->phone_number);
    $email = htmlspecialchars($request->email);
    $color = $this->randomColor();

    $insert = Employee::create([
      'employee_identity_number' => $ein,
      'name' => $firstName . ' ' . $lastName,
      'first_name' => $firstName,
      'last_name' => (is_null($lastName) || empty($lastName)) ? null : $lastName,
      'email' => $email,
      'phone_number' => $phoneNumber,
      'color' => $color
    ]);

    if ($insert) {
      $json = ['status' => 200, 'message' => 'Data berhasil disimpan'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal disimpan'];
    }

    return response()->json($json);
  }

  /** random color
   *
   */
  private function randomColor()
  {
    return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
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
    $data = Employee::where('id', $id)->first();

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
   * @param VideoConferenceRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(VideoConferenceRequest $request)
  {
    $id = $request->id;
    $firstName = htmlspecialchars($request->first_name);
    $lastName = htmlspecialchars($request->last_name);
    $ein = htmlspecialchars($request->employee_identity_number);
    $phoneNumber = htmlspecialchars($request->phone_number);
    $email = htmlspecialchars($request->email);

    $update = Employee::where('id', $id)->first()->update([
      'employee_identity_number' => $ein,
      'name' => $firstName . ' ' . $lastName,
      'first_name' => $firstName,
      'last_name' => (is_null($lastName) || empty($lastName)) ? null : $lastName,
      'email' => $email,
      'phone_number' => $phoneNumber
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
    $delete = Employee::where('id', $id)->first()->delete();

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
    $data = Employee::where('email', $email)->first();

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
    $data = Employee::where('phone_number', $phoneNumber)->first();

    if (is_null($data)) {
      $json = ['status' => 200, 'message' => 'Nomor Handphone tersedia'];
    } else {
      $json = ['status' => 500, 'message' => 'Nomor Handphone sudah digunakan'];
    }

    return response()->json($json);
  }
}
