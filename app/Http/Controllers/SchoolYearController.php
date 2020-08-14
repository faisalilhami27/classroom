<?php

namespace App\Http\Controllers;

use App\Http\Requests\SchoolYearRequest;
use App\Models\SchoolYear;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SchoolYearController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index()
  {
    $title = 'Daftar Tahun Ajar';
    return view('backend.schoolYear.index', compact('title'));
  }

  /**
   * Show data in datatable.
   *
   */
  public function datatable()
  {
    $data = SchoolYear::orderBy('id', 'desc')->get();
    return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('semester', function ($query) {
        $semester = null;

        if ($query->semester == 1) {
          $semester = 'Ganjil';
        } else {
          $semester = 'Genap';
        }
        return $semester;
      })
      ->addColumn('status_active', function ($query) {
        $status = null;

        if ($query->status_active == 1) {
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

        if ($query->status_active == 1) {
          $button = '<a href="#" class="btn btn-success btn-sm btn-edit" title="Edit Data" id="'. $query->id .'" onclick="editData('. $query->id .')"><i class="icon icon-pencil-square-o"></i></a>';
        } else {
          if ($updateDelete) {
            $button = '<a href="#" class="btn btn-warning btn-sm btn-switch" title="Ubah Status" id="'. $query->id .'" onclick="changeStatus('. $query->id .')"><i class="icon icon-power-off"></i></a>
                       <a href="#" class="btn btn-success btn-sm btn-edit" title="Edit Data" id="'. $query->id .'" onclick="editData('. $query->id .')"><i class="icon icon-pencil-square-o"></i></a>
                       <a href="#" class="btn btn-danger btn-sm" id="'. $query->id .'" onclick="deleteData('. $query->id .')" title="Delete Data"><i class="icon icon-trash-o"></i></a>';
          } else if ($update) {
            $button = '<a href="#" class="btn btn-warning btn-sm btn-switch" title="Ubah Status" id="'. $query->id .'" onclick="changeStatus('. $query->id .')"><i class="icon icon-power-off"></i></a>
                       <a href="#" class="btn btn-success btn-sm btn-edit" title="Edit Data" id="'. $query->id .'" onclick="editData('. $query->id .')"><i class="icon icon-pencil-square-o"></i></a>';
          } else if ($delete) {
            $button = '<a href="#" class="btn btn-success btn-sm btn-edit" title="Ubah Status" id="'. $query->id .'" onclick="changeStatus('. $query->id .')"><i class="icon icon-power-off"></i></a>
                       <a href="#" class="btn btn-danger btn-sm" id="'. $query->id .'" onclick="deleteData('. $query->id .')" title="Delete Data"><i class="icon icon-trash-o"></i></a>';
          } else {
            $button = '<a href="#" class="btn btn-warning btn-sm btn-switch" title="Ubah Status" id="'. $query->id .'" onclick="changeStatus('. $query->id .')"><i class="icon icon-power-off"></i></a>';
          }
        }
        return $button;
      })
      ->rawColumns(['status_active', 'action'])
      ->make(true);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param SchoolYearRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(SchoolYearRequest $request)
  {
    $earlyYear = $request->early_year;
    $endYear = $request->end_year;
    $semester = $request->semester;
    $checkDuplicate = SchoolYear::where('early_year', $earlyYear)
      ->where('end_year', $endYear)
      ->where('semester', $semester)
      ->first();
    $checkOddOrEven = ($semester == 1) ? 'Ganjil' : 'Genap';

    /* check duplicate data */
    if (!is_null($checkDuplicate)) {
      return response()->json(['status' => 500, 'message' => 'Semester ' . $checkOddOrEven . ' pada tahun ajaran ini sudah ada']);
    }

    $insert = SchoolYear::create([
      'early_year' => $earlyYear,
      'end_year' => $endYear,
      'semester' => $semester,
      'status_active' => 0,
      'status_passed' => 0
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
    $data = SchoolYear::find($id);

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
   * @param SchoolYearRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(SchoolYearRequest $request)
  {
    $id = $request->id;
    $earlyYear = $request->early_year;
    $endYear = $request->end_year;
    $semester = $request->semester;

    $update = SchoolYear::find($id)->update([
      'early_year' => $earlyYear,
      'end_year' => $endYear,
      'semester' => $semester
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
    $delete = SchoolYear::find($id);
    $delete->delete();

    if ($delete) {
      $json = ['status' => 200, 'message' => 'Data berhasil dihapus'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal dihapus'];
    }

    return response()->json($json);
  }

  /**
   * change status school year
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function changeStatus(Request $request)
  {
    $id = $request->id;

    /* non active the previous school year */
    SchoolYear::where('status_active', 1)->update([
      'status_active' => 0,
      'status_passed' => 2
    ]);

    /* enable status of the new school year */
    $update = SchoolYear::where('id', $id)->update([
      'status_active' => 1,
      'status_passed' => 0
    ]);

    if ($update) {
      $json = ['status' => 200, 'message' => 'Tahun ajar berhasil diubah'];
    } else {
      $json = ['status' => 500, 'message' => 'Tahun ajar gagal diubah'];
    }

    return response()->json($json);
  }
}
