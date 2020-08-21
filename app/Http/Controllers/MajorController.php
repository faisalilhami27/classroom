<?php

namespace App\Http\Controllers;

use App\Http\Requests\MajorImportRequest;
use App\Http\Requests\MajorRequest;
use App\Imports\MajorImport;
use App\Models\Major;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class MajorController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index()
  {
    $title = 'Daftar Jurusan';
    return view('backend.major.index', compact('title'));
  }

  /**
   * import data major from excel
   * @param MajorImportRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function import(MajorImportRequest $request)
  {
    $file = $request->file('major_import');

    $import = Excel::import(new MajorImport(), $file);

    if ($import) {
      return response()->json(['status' => 200, 'message' => 'Data berhasil diimport']);
    } else {
      return response()->json(['status' => 500, 'message' => 'Data gagal diimport']);
    }
  }

  /**
   * Show data in datatable.
   *
   */
  public function datatable()
  {
    $data = Major::orderBy('id', 'desc')->get();
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
   * @param MajorRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(MajorRequest $request)
  {
    $data = $request->all();
    $insert = Major::create($data);

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
    $data = Major::where('id', $id)->first();

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
  public function update(MajorRequest $request)
  {
    $id = $request->id;
    $data = $request->all();
    $update = Major::where('id', $id)->first()->update($data);

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
    $delete = Major::where('id', $id)->first();
    $delete->delete();

    if ($delete) {
      $json = ['status' => 200, 'message' => 'Data berhasil dihapus'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal dihapus'];
    }

    return response()->json($json);
  }
}
