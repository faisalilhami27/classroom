<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubjectImportRequest;
use App\Http\Requests\SubjectRequest;
use App\Imports\SubjectImport;
use App\Models\Major;
use App\Models\Semester;
use App\Models\Subject;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class SubjectController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index()
  {
    $title = (optional(configuration())->type_school == 1) ? 'Daftar Mata Kuliah' : 'Daftar Mata Pelajaran';
    $semesters = Semester::all();
    $majors = Major::all();
    return view('backend.subject.index', compact('title', 'semesters', 'majors'));
  }

  /**
   * import data major from excel
   * @param SubjectImportRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function import(SubjectImportRequest $request)
  {
    $file = $request->file('file_import');
    $major = $request->major_id_import;

    $import = Excel::import(new SubjectImport($major), $file);

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
    $data = Subject::with(['semester', 'major'])
      ->orderBy('id', 'desc')
      ->get();

    return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('major', function ($query) {
        if (is_null($query->major_id)) {
          return 'Semua Jurusan';
        } else {
          return $query->major->name;
        }
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
   * @param SubjectRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(SubjectRequest $request)
  {
    $semester = $request->semester_id;
    $major = $request->major_id;
    $code = $request->code;
    $name = $request->name;

    $insert = Subject::create([
      'code' => strtoupper($code),
      'name' => ucwords($name),
      'semester_id' => (is_null($semester)) ? null : $semester,
      'major_id' => ($major == 0) ? null : $major,
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
    $data = Subject::where('id', $id)->first();

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
   * @param SubjectRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(SubjectRequest $request)
  {
    $id = $request->id;
    $semester = $request->semester_id;
    $major = $request->major_id;
    $code = $request->code;
    $name = $request->name;

    $update = Subject::where('id', $id)->first()->update([
      'code' => strtoupper($code),
      'name' => ucwords($name),
      'semester_id' => (is_null($semester)) ? null : $semester,
      'major_id' => ($major == 0) ? null : $major,
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
    $delete = Subject::where('id', $id)->first();
    $delete->delete();

    if ($delete) {
      $json = ['status' => 200, 'message' => 'Data berhasil dihapus'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal dihapus'];
    }

    return response()->json($json);
  }
}
