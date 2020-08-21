<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserEmployeeRequest;
use App\Http\Requests\UserEmployeeUpdateRequest;
use App\Models\Configuration;
use App\Models\Employee;
use App\Models\Role;
use App\Models\UserEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use MacsiDigital\Zoom\Facades\Zoom;
use Yajra\DataTables\DataTables;

class UserEmployeeController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index()
  {
    $title = 'Daftar User Karyawan';
    if (Session::get('role_id') != 1) {
      $roles = Role::where('id', '!=', 1)->get();
    } else {
      $roles = Role::all();
    }
    return view('backend.userEmployee.index', compact('title', 'roles'));
  }

  /**
   * Show data in datatable.
   *
   */
  public function datatable()
  {
    $userEmployee = UserEmployee::with('employee')
      ->orderBy('id', 'desc');

    if (Session::get('role_id') != 1) {
      $userEmployee->where('id', '!=', 1);
    }

    $data = $userEmployee->get();
    return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('permission', function ($query) {
        $roles = $query->roles()->get();
        if (!is_null($roles)) {
          $numRole = count($roles);
          if ($numRole > 1) {
            return $numRole . ' hak akses';
          }

          $data = [];
          foreach ($roles as $role) {
            array_push($data, $role->name);
          }
          return $data;
        }
        return '-';
      })
      ->addColumn('status', function ($query) {
        $status = null;

        if ($query->status == 1) {
          $status = '<span class="label label-primary">Aktif</span>';
        } else {
          $status = '<span class="label label-danger">Tidak Aktif</span>';
        }

        return $status;
      })
      ->addColumn('action', function ($query) {
        $updateDelete = checkPermission()->update_delete;
        $update = checkPermission()->update;
        $delete = checkPermission()->delete;
        $button = null;

        if ($updateDelete) {
          $button = '<a href="#" class="btn btn-info btn-sm btn-reset" title="Reset Password" id="' . $query->id . '" onclick="resetPassword(' . $query->id . ')"><i class="icon icon-refresh"></i></a>
                     <a href="#" class="btn btn-success btn-sm btn-edit" title="Edit Data" id="${data.id}" onclick="editData(' . $query->id . ')"><i class="icon icon-pencil-square-o"></i></a>
                     <a href="#" class="btn btn-danger btn-sm" id="' . $query->id . '" onclick="deleteData(' . $query->id . ')" title="Delete Data"><i class="icon icon-trash-o"></i></a>';
        } else if ($update) {
          $button = '<a href="#" class="btn btn-info btn-sm btn-reset" title="Reset Password" id="' . $query->id . '" onclick="resetPassword(' . $query->id . ')"><i class="icon icon-refresh"></i></a>
                     <a href="#" class="btn btn-success btn-sm btn-edit" title="Edit Data" id="${data.id}" onclick="editData(' . $query->id . ')"><i class="icon icon-pencil-square-o"></i></a>';
        } else if ($delete) {
          $button = '<a href="#" class="btn btn-info btn-sm btn-reset" title="Reset Password" id="' . $query->id . '" onclick="resetPassword(' . $query->id . ')"><i class="icon icon-refresh"></i></a>
                     <a href="#" class="btn btn-danger btn-sm" id="' . $query->id . '" onclick="deleteData(' . $query->id . ')" title="Delete Data"><i class="icon icon-trash-o"></i></a>';
        } else {
          $button = '<a href="#" class="btn btn-info btn-sm btn-reset" title="Reset Password" id="' . $query->id . '" onclick="resetPassword(' . $query->id . ')"><i class="icon icon-refresh"></i></a>';
        }
        return $button;
      })
      ->rawColumns(['status', 'action'])
      ->make(true);
  }

  /**
   * get employee unregistered
   */
  public function getUnregisteredEmployees()
  {
    $rawEmployees = Employee::with('userEmployee')->get();
    $employees = [];
    foreach ($rawEmployees as $employee) {
      if (is_null($employee->userEmployee)) {
        $employees[] = $employee;
      }
    }

    $data = [];
    if (!empty($employees)) {
      foreach ($employees as $employee) {
        /* check whether the staff already has an account or not */
        $exists = UserEmployee::where('employee_id', $employee->id)
          ->first();

        if (!$exists) {
          $data[] = $employee;
        }
      }
    }

    return response()->json($data);
  }

  /**
   * reset password
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function resetPassword(Request $request)
  {
    $password = Configuration::first()->reset_password_employee;

    if (is_null($password)) {
      return response()->json(['status' => 500, 'message' => 'Silahkan isi reset password di konfigurasi']);
    }

    $id = $request->id;
    $data = UserEmployee::where('id', $id)->update(['password' => Hash::make($password)]);

    if ($data) {
      $json = ['status' => 200, 'message' => 'Password berhasil reset'];
    } else {
      $json = ['status' => 500, 'message' => 'Password gagal reset'];
    }

    return response()->json($json);
  }

  /**
   * check duplicate username
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function checkUsername(Request $request)
  {
    $username = $request->username;
    $data = UserEmployee::where('username', $username)->first();

    if (is_null($data)) {
      $json = ['status' => 200, 'message' => 'Username tersedia'];
    } else {
      $json = ['status' => 500, 'message' => 'Username sudah digunakan'];
    }

    return response()->json($json);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(UserEmployeeRequest $request)
  {
    $employeeId = $request->employee_id;
    $username = htmlspecialchars($request->username);
    $password = htmlspecialchars($request->password);
    $roles = $request->role_id;
    $status = $request->status;

    $userEmployee = UserEmployee::create([
      'employee_id' => $employeeId,
      'username' => $username,
      'password' => Hash::make($password),
      'status' => $status,
      'status_generate' => 0
    ]);

    // insert the role to role user table
    $userEmployee->roles()->attach($roles);

    if ($userEmployee) {
      $json = ['status' => 200, 'message' => 'Data berhasil ditambah'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal ditambah'];
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
    $userEmployee = UserEmployee::where('id', $id)->first();
    $roles = $userEmployee->roles()->get();
    $data = [];

    foreach ($roles as $role) {
      $data[] = $role->pivot->role_id;
    }

    if ($userEmployee) {
      $json = ['status' => 200, 'data' => $userEmployee, 'roles' => $data];
    } else {
      $json = ['status' => 500, 'message' => 'Data tidak ditemukan'];
    }

    return response()->json($json);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param UserEmployeeUpdateRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(UserEmployeeUpdateRequest $request)
  {
    $id = $request->id;
    $username = htmlspecialchars($request->username);
    $roles = $request->role_id;
    $status = $request->status;

    $userEmployee = UserEmployee::find($id);
    $update = $userEmployee->update([
      'username' => $username,
      'status' => $status
    ]);

    // update the role to role user table
    $userEmployee->roles()->sync($roles);

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
    $user = UserEmployee::where('id', $id)->first();
    $delete = $user->delete();
    $user->employee->where('id', $user->employee_id)->delete();

    if (!is_null($user->user_id_zoom)) {
      Zoom::user()->find($user->user_id_zoom)->delete();
    }

    if ($delete) {
      $json = ['status' => 200, 'message' => 'Data berhasil dihapus'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal dihapus'];
    }

    return response()->json($json);
  }
}
