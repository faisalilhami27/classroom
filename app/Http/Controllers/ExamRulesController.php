<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExamRulesRequest;
use App\Models\ExamRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class ExamRulesController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index()
  {
    $title = 'Daftar Peraturan Ujian';
    return view('backend.cbt.rules', compact('title'));
  }

  /**
   * Show data in datatable.
   *
   */
  public function datatable()
  {
    $data = ExamRules::orderBy('id', 'desc')->get();
    return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('action', function ($query) {
        $updateDelete = checkPermission()->update_delete;
        $update = checkPermission()->update;
        $delete = checkPermission()->delete;
        $button = null;

        if ($updateDelete) {
          $button = '<a href="#" class="btn btn-success btn-sm btn-edit" title="Edit Data" id="' . $query->id . '" onclick="editData(' . $query->id . ')"><i class="icon icon-pencil-square-o"></i></a>
                     <a href="#" class="btn btn-danger btn-sm" id="' . $query->id . '" onclick="deleteData(' . $query->id . ')" title="Delete Data"><i class="icon icon-trash-o"></i></a>';
        } else if ($update) {
          $button = '<a href="#" class="btn btn-success btn-sm btn-edit" title="Edit Data" id="' . $query->id . '" onclick="editData(' . $query->id . ')"><i class="icon icon-pencil-square-o"></i></a>';
        } else if ($delete) {
          $button = '<a href="#" class="btn btn-danger btn-sm" id="' . $query->id . '" onclick="deleteData(' . $query->id . ')" title="Delete Data"><i class="icon icon-trash-o"></i></a>';
        } else {
          $button = 'Tidak ada aksi';
        }
        return $button;
      })
      ->rawColumns(['name', 'action'])
      ->make(true);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param ExamRulesRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(ExamRulesRequest $request)
  {
    $text = $request->text;
    $name = $request->name;

    $insert = ExamRules::create([
      'text' => $text,
      'name' => $name,
      'employee_id' => Auth::user()->employee_id,
      'created_by' => Auth::user()->employee_id
    ]);

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
    $data = ExamRules::find($id);
    $convertText = htmlspecialchars($data->text);
    $textReplace = trim(preg_replace('/\s\s+/', ' ', $convertText));

    if ($data) {
      $json = ['status' => 200, 'data' => $data, 'text' => $textReplace];
    } else {
      $json = ['status' => 500, 'message' => 'Data tidak ditemukan'];
    }

    return response()->json($json);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param ExamRulesRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(ExamRulesRequest $request)
  {
    $id = $request->id;
    $text = $request->text;
    $name = $request->name;

    $update = ExamRules::find($id)->update([
      'text' => $text,
      'name' => $name,
      'last_updated_by' => Auth::user()->employee_id
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
    $delete = ExamRules::find($id);
    $delete->delete();

    if ($delete) {
      $json = ['status' => 200, 'message' => 'Data berhasil dihapus'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal dihapus'];
    }

    return response()->json($json);
  }
}
