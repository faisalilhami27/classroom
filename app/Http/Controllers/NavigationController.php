<?php

namespace App\Http\Controllers;

use App\Http\Requests\NavigationRequest;
use App\Models\Navigation;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class NavigationController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index()
  {
    $title = 'Daftar Navigasi';
    $navigations = Navigation::where('parent_id', 0)->get(); // get data navigation to show combobox
    return view('backend.navigation.index', compact('title', 'navigations'));
  }

  /**
   * Show data in datatable.
   *
   */
  public function datatable()
  {
    $data = Navigation::orderBy('id', 'desc')->get();
    return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('icon', function ($query) {
        $icon = null;

        if (!is_null($query->icon)) {
          $icon = '<span class="' . $query->icon . '"></span>';
        } else {
          $icon = '-';
        }
        return $icon;
      })
      ->addColumn('order_num', function ($query) {
        $number = null;
        if (is_null($query->order_num)) {
          $number = '-';
        } else {
          $number = $query->order_num;
        }
        return $number;
      })
      ->addColumn('order_sub', function ($query) {
        $number = null;
        if (is_null($query->order_sub)) {
          $number = '-';
        } else {
          $number = $query->order_sub;
        }
        return $number;
      })
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
      ->rawColumns(['icon', 'action'])
      ->make(true);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param NavigationRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(NavigationRequest $request)
  {
    $title = $request->title;
    $url = $request->url;
    $icon = $request->icon;
    $orderNum = $request->order_num;
    $orderSub = $request->order_sub;
    $parentId = $request->parent_id;

    $data = [
      'title' => $title,
      'url' => $url,
      'icon' => $icon,
      'parent_id' => $parentId,
      'order_num' => (is_null($orderNum)) ? null : $orderNum,
      'order_sub' => (is_null($orderSub)) ? null : $orderSub
    ];

    $insert = Navigation::create($data);

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
    $data = Navigation::find($id);

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
  public function update(NavigationRequest $request)
  {
    $id = $request->id;
    $title = $request->title;
    $url = $request->url;
    $icon = $request->icon;
    $orderNum = $request->order_num;
    $orderSub = $request->order_sub;
    $parentId = $request->parent_id;

    $data = [
      'title' => $title,
      'url' => $url,
      'icon' => $icon,
      'parent_id' => $parentId,
      'order_num' => (is_null($orderNum)) ? null : $orderNum,
      'order_sub' => (is_null($orderSub)) ? null : $orderSub
    ];

    $insert = Navigation::find($id)->update($data);

    if ($insert) {
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
    $delete = Navigation::find($id);
    $delete->delete();

    if ($delete) {
      $json = ['status' => 200, 'message' => 'Data berhasil dihapus'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal dihapus'];
    }

    return response()->json($json);
  }
}
