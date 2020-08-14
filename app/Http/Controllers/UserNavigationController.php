<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserNavigationRequest;
use App\Models\Navigation;
use App\Models\Role;
use App\Models\UserNavigation;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class UserNavigationController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index()
  {
    $title = 'User Navigation';
    $roles = Role::all(); // get data role to show in combobox
    $navigations = Navigation::all(); // get data navigation to show in combobox
    return view('backend.userNavigation.index', compact('title', 'roles', 'navigations'));
  }

  /**
   * datatable user navigation.
   *
   */
  public function datatable()
  {
    $data = UserNavigation::with(['navigation', 'role'])
      ->orderBy('id', 'desc')
      ->get();
    return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('create', function ($query) {
        $create = null;

        if ($query->navigation->url == '#' || $query->navigation->url == 'configuration.index') {
          $create = '<label class="switch switch-primary">
                        <input class="switch-input" disabled type="checkbox" id="' . $query->id . '" value="create">
                        <span class="switch-track"></span>
                        <span class="switch-thumb"></span>
                       </label>';
        } else {
          if ($query->create == 1) {
            $create = '<label class="switch switch-primary">
                        <input class="switch-input" type="checkbox" id="' . $query->id . '" checked="checked" value="create">
                        <span class="switch-track"></span>
                        <span class="switch-thumb"></span>
                       </label>';
          } else {
            $create = '<label class="switch switch-primary">
                        <input class="switch-input" type="checkbox" id="' . $query->id . '" value="create">
                        <span class="switch-track"></span>
                        <span class="switch-thumb"></span>
                       </label>';
          }
        }
        return $create;
      })
      ->addColumn('update', function ($query) {
        $update = null;

        if ($query->navigation->url == '#' || $query->navigation->url == 'configuration.index') {
          $update = '<label class="switch switch-success">
                        <input class="switch-input" disabled type="checkbox" id="' . $query->id . '" value="update">
                        <span class="switch-track"></span>
                        <span class="switch-thumb"></span>
                       </label>';
        } else {
          if ($query->update == 1) {
            $update = '<label class="switch switch-success">
                        <input class="switch-input" type="checkbox" id="' . $query->id . '" checked="checked" value="update">
                        <span class="switch-track"></span>
                        <span class="switch-thumb"></span>
                       </label>';
          } else {
            $update = '<label class="switch switch-success">
                        <input class="switch-input" type="checkbox" id="' . $query->id . '" value="update">
                        <span class="switch-track"></span>
                        <span class="switch-thumb"></span>
                       </label>';
          }
        }
        return $update;
      })
      ->addColumn('delete', function ($query) {
        $delete = null;

        if ($query->navigation->url == '#' || $query->navigation->url == 'configuration.index') {
          $delete = '<label class="switch switch-danger">
                        <input class="switch-input" disabled type="checkbox" id="' . $query->id . '" value="delete">
                        <span class="switch-track"></span>
                        <span class="switch-thumb"></span>
                       </label>';
        } else {
          if ($query->delete == 1) {
            $delete = '<label class="switch switch-danger">
                        <input class="switch-input" type="checkbox" id="' . $query->id . '" checked="checked" value="delete">
                        <span class="switch-track"></span>
                        <span class="switch-thumb"></span>
                       </label>';
          } else {
            $delete = '<label class="switch switch-danger">
                        <input class="switch-input" type="checkbox" id="' . $query->id . '" value="delete">
                        <span class="switch-track"></span>
                        <span class="switch-thumb"></span>
                       </label>';
          }
        }

        return $delete;
      })
      ->addColumn('action', function ($query) {
        return '<a href="#" class="btn btn-danger btn-sm" id="' . $query->id . '" onclick="deleteData(' . $query->id . ')" title="Delete Data"><i class="icon icon-trash-o"></i></a>';;
      })
      ->rawColumns(['create', 'update', 'delete', 'action'])
      ->make(true);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param UserNavigationRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(UserNavigationRequest $request)
  {
    $role = $request->role_user_id;
    $navigation = $request->navigation_id;
    $create = $request->create;
    $update = $request->update;
    $delete = $request->destroy;
    $checkDuplicate = UserNavigation::where('role_id', $role)
      ->where('navigation_id', $navigation)
      ->first();

    // check duplicate
    if (!is_null($checkDuplicate)) {
      return response()->json(['status' => 500, 'message' => 'Data sudah ada pada sistem']);
    }

    $insert = UserNavigation::create([
      'role_id' => $role,
      'navigation_id' => $navigation,
      'create' => $create,
      'update' => $update,
      'delete' => $delete
    ]);

    if ($insert) {
      $json = ['status' => 200, 'message' => 'Data berhasil disimpan'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal disimpan'];
    }

    return response()->json($json);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(Request $request)
  {
    $id = $request->id;
    $type = $request->type;
    $value = $request->value;
    $status = null;

    // check type wheter create, update or delete
    if ($type == 'create') {
      $status = ['create' => $value];
    } else if ($type == 'update') {
      $status = ['update' => $value];
    } else {
      $status = ['delete' => $value];
    }

    // update data to database
    $data = UserNavigation::find($id)->update($status);

    if ($data) {
      $json = ['status' => 200, 'message' => 'Data berhasil disimpan'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal disimpan'];
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
    $delete = UserNavigation::find($id)->delete();

    if ($delete) {
      $json = ['status' => 200, 'message' => 'Data berhasil dihapus'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal dihapus'];
    }

    return response()->json($json);
  }
}
