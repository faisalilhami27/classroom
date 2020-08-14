<?php

namespace App\Http\Controllers;

use App\Http\Requests\SemesterRequest;
use App\Models\Semester;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SemesterController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index()
  {
    $title = 'Daftar Semester';
    return view('backend.semester.index', compact('title'));
  }

  /**
   * Show data in datatable.
   *
   */
  public function datatable()
  {
    $data = Semester::orderBy('id', 'desc')->get();
    return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('number', function ($query) {
        return 'Semester ' . $query->number;
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
      ->make(true);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param SemesterRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(SemesterRequest $request)
  {
    $data = $request->all();
    $insert = Semester::create($data);

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
    $data = Semester::find($id);

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
   * @param SemesterRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(SemesterRequest $request)
  {
    $id = $request->id;
    $data = $request->all();
    $update = Semester::find($id)->update($data);

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
    $delete = Semester::find($id);
    $delete->delete();

    if ($delete) {
      $json = ['status' => 200, 'message' => 'Data berhasil dihapus'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal dihapus'];
    }

    return response()->json($json);
  }
}
