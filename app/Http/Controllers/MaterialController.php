<?php

namespace App\Http\Controllers;

use App\Http\Requests\MaterialRequest;
use App\Models\GradeLevel;
use App\Models\Major;
use App\Models\Material;
use App\Models\SchoolYear;
use App\Models\Semester;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class MaterialController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index()
  {
    $title = 'Daftar Materi E-Learning';
    $subjects = Subject::all();
    $semesters = Semester::all();
    $gradeLevels = GradeLevel::all();
    $schoolYears = SchoolYear::all();
    $majors = Major::all();
    return view('backend.e-learning.index', compact('title', 'subjects', 'semesters', 'gradeLevels', 'schoolYears', 'majors'));
  }

  /**
   * get subject
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getSubject(Request $request)
  {
    $semesterId = $request->semester_id;
    $majorId = $request->major_id;
    $subjects = Subject::where('semester_id', $semesterId)
      ->where('major_id', $majorId)
      ->get();

    if ($subjects) {
      $json = ['status' => 200, 'data' => $subjects];
    } else {
      $json = ['status' => 500, 'message' => 'Data tidak ditemukan'];
    }

    return response()->json($json);
  }

  /**
   * Show data in datatable.
   * @param Request $request
   * @return
   * @throws \Exception
   */
  public function datatable(Request $request)
  {
    $level = $request->level;
    $subject = $request->subject;
    $material = Material::with(['subject', 'semester', 'gradeLevel'])
      ->orderBy('position', 'asc');

    /* filter by on semester or grade level */
    if ($level != 'all') {
      if (optional(configuration())->type_school == 1) {
        $material->where('semester_id', $level);
      } else {
        $material->where('grade_level_id', $level);
      }
    }

    /* filter by on subject */
    if ($subject != 'all') {
      $material->where('subject_id', $subject);
    }

    $data = $material->get();
    return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('level', function ($query) {
        if (configuration()->type_school == 1) {
          return $query->semester->number;
        } else {
          return $query->gradeLevel->name;
        }
      })
      ->addColumn('source', function ($query) {
        if (!is_null($query->module) && !is_null($query->archive) && !is_null($query->video_link)) {
          return '<span class="badge badge-success">Modul</span> <span class="badge badge-primary">Source Code</span> <span class="badge badge-info">Link Video</span>';
        } elseif (!is_null($query->module) && !is_null($query->archive)) {
          return '<span class="badge badge-success">Modul</span> <span class="badge badge-primary">Source Code</span>';
        } elseif (!is_null($query->module) && !is_null($query->video_link)) {
          return '<span class="badge badge-success">Modul</span> <span class="badge badge-info">Link Video</span>';
        } elseif (!is_null($query->archive) && !is_null($query->video_link)) {
          return '<span class="badge badge-primary">Source Code</span> <span class="badge badge-info">Link Video</span>';
        } elseif (!is_null($query->module)) {
          return '<span class="badge badge-success">Modul</span>';
        } elseif (!is_null($query->archive)) {
          return '<span class="badge badge-primary">Source Code</span>';
        } else {
          return '<span class="badge badge-info">Link Video</span>';
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
      ->rawColumns(['level', 'source', 'action'])
      ->make(true);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param MaterialRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(MaterialRequest $request)
  {
    $semesterId = $request->semester_id;
    $gradeLevelId = $request->grade_level_id;
    $subjectId = $request->subject_id;
    $majorId = $request->major_id;
    $schoolYearId = $request->school_year_id;
    $position = $request->position;
    $title = $request->title;
    $content = $request->detail_material;
    $videoLink = $request->video_link;
    $module = $request->file('module');
    $archive = $request->file('archive');
    $checkPosition = Material::where('position', $position)
      ->where('employee_id', Auth::user()->employee_id)
      ->first();

    /* check the position whether has used or not */
    if (!is_null($checkPosition)) {
      return response()->json(['status' => 500, 'message' => 'Urutan materi sudah dipakai']);
    }

    /* check if all source of material is null */
    if (is_null($videoLink) && is_null($module) && is_null($archive)) {
      return response()->json(['status' => 500, 'message' => 'Harus ada salah satu sumber materi yang dipilih']);
    }

    $insert = Material::create([
      'semester_id' => (is_null($semesterId)) ? null : $semesterId,
      'grade_level_id' => (is_null($gradeLevelId)) ? null : $gradeLevelId,
      'subject_id' => $subjectId,
      'major_id' => $majorId,
      'school_year_id' => $schoolYearId,
      'employee_id' => Auth::user()->employee_id,
      'position' => $position,
      'title' => $title,
      'content' => $content,
      'video_link' => $videoLink,
      'created_by' => Auth::user()->employee_id,
      'module' => (is_null($module)) ? null : $this->storeFile($module, 'e-learning/module'),
      'archive' => (is_null($archive)) ? null : $this->storeFile($archive, 'e-learning/archive')
    ]);

    if ($insert) {
      $json = ['status' => 200, 'message' => 'Data berhasil disimpan'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal disimpan'];
    }

    return response()->json($json);
  }

  /**
   * store file
   * @param $file
   * @param $url
   * @return
   */
  private function storeFile($file, $url)
  {
    $filename = $file->getClientOriginalName();
    $replaceFileName = pathinfo($filename, PATHINFO_FILENAME) . '_' . time() . '.' . $file->getClientOriginalExtension();
    return $file->storeAs($url, $replaceFileName);
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
    $data = Material::find($id);
    $subjects = Subject::where('semester_id', optional($data)->semester_id)
      ->orWhere('semester_id', null)
      ->get();

    if ($data) {
      $json = ['status' => 200, 'data' => $data, 'subject' => $subjects];
    } else {
      $json = ['status' => 500, 'message' => 'Data tidak ditemukan'];
    }

    return response()->json($json);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param MaterialRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(MaterialRequest $request)
  {
    $id = $request->id;
    $semesterId = $request->semester_id;
    $gradeLevelId = $request->grade_level_id;
    $subjectId = $request->subject_id;
    $majorId = $request->major_id;
    $schoolYearId = $request->school_year_id;
    $position = $request->position;
    $title = $request->title;
    $content = $request->detail_material;
    $videoLink = $request->video_link;
    $module = $request->file('module');
    $archive = $request->file('archive');
    $data = Material::find($id);
    $checkPosition = Material::where('position', $position)
      ->where('employee_id', Auth::user()->employee_id)
      ->first();

    /* check the position whether has used or not */
    if ($checkPosition) {
      if ($checkPosition->id != $id) {
        return response()->json(['status' => 500, 'message' => 'Urutan materi sudah dipakai']);
      }
    }

    /* check if video link is null */
    if (is_null($videoLink)) {
      /* check if the module file and archive file is null */
      if (is_null($data->module) && is_null($data->archive)) {
        return response()->json(['status' => 500, 'message' => 'Harus ada salah satu sumber materi yang dipilih']);
      }
    }

    /* check if module is null */
    if (is_null($module)) {
      $moduleFile = (!is_null($data->module)) ? $data->module : null;
    } else {
      /* delete old files */
      if (!is_null($data->module)) {
        Storage::disk('public')->delete($data->module);
      }
      $moduleFile = $this->storeFile($module, 'e-learning/module');
    }

    /* check if archive is null */
    if (is_null($archive)) {
      $archiveFile = (!is_null($data->archive)) ? $data->archive : null;
    } else {
      /* delete old files */
      if (!is_null($data->archive)) {
        Storage::disk('public')->delete($data->archive);
      }
      $archiveFile = $this->storeFile($archive, 'e-learning/archive');
    }

    $update = $data->update([
      'semester_id' => (is_null($semesterId)) ? null : $semesterId,
      'grade_level_id' => (is_null($gradeLevelId)) ? null : $gradeLevelId,
      'subject_id' => $subjectId,
      'major_id' => $majorId,
      'school_year_id' => $schoolYearId,
      'position' => $position,
      'title' => $title,
      'content' => $content,
      'video_link' => $videoLink,
      'module' => $moduleFile,
      'archive' => $archiveFile,
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
    $delete = Material::find($id);
    $delete->delete();

    if ($delete) {
      $json = ['status' => 200, 'message' => 'Data berhasil dihapus'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal dihapus'];
    }

    return response()->json($json);
  }
}
