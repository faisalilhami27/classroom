<?php

namespace App\Http\Controllers;

use App\Http\Requests\NavigationRequest;
use App\Http\Requests\RoleRequest;
use App\Models\Role;
use App\Models\RoleUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class RoleController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index()
  {
    $title = 'Daftar Role';
    return view('backend.role.index', compact('title'));
  }

  /**
   * Show data in datatable.
   *
   */
  public function datatable()
  {
    $data = Role::where('id', '!=' , 1)
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
      })
      ->make(true);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param RoleRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(RoleRequest $request)
  {
    $data = $request->all();
    $insert = Role::create($data);

    if ($insert) {
      $json = ['status' => 200, 'message' => 'Data berhasil disimpan'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal disimpan'];
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
    $data = Role::find($id);

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
   * @param NavigationRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(RoleRequest $request)
  {
    $id = $request->id;
    $data = $request->all();
    $update = Role::find($id)->update($data);

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
    $delete = Role::find($id);
    $delete->delete();

    if ($delete) {
      $json = ['status' => 200, 'message' => 'Data berhasil dihapus'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal dihapus'];
    }

    return response()->json($json);
  }

  /**
   * method for selecting roles if there are more than one role
   * @param Request $request
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function chooseRole(Request $request)
  {
    $role = null;
    $accessList = null;
    $user = Auth::id();

    if (Auth::guard('employee')->check()) {
      $accessList = RoleUser::where('user_employee_id', $user)
        ->with(['role', 'userEmployee'])
        ->get();

      $role = RoleUser::where('user_employee_id', $user)
        ->with(['role', 'userEmployee'])
        ->first();
    }

    /**
     * Instant pick role for only had 1 access role
     */
    if (count($accessList) == 1) {
      $request->role_id = $role->role_id;
      return $this->pickRole($request);
    }

    return view('backend.role.chooseRole', compact('accessList'));
  }

  /**
   * method for redirect to home page
   * @param Request $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function pickRole(Request $request)
  {
    $request->session()->forget('role_id');
    $request->session()->forget('guard');
    $roleId = $request->role_id;

    $guard = null;
    if (Auth::guard('employee')->check()) {
      $guard = 'employee';
    }

    $request->session()->put('role_id', $roleId);
    $request->session()->put('guard', $guard);

    return redirect()->route('dashboard');
  }
}
