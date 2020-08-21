<?php

namespace App\Http\Controllers;

use App\Http\Requests\MinimalCompletenessCriteriaRequest;
use App\Models\GradeLevel;
use App\Models\SchoolYear;
use App\Models\Semester;
use App\Models\MinimalCompletenessCriteria;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class MinimalCompletenessCriteriaController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index()
  {
    $title = 'Daftar KKM';
    $semesters = Semester::all();
    $gradeLevels = GradeLevel::all();

    return view('backend.minimalCriteria.index', compact('title', 'semesters', 'gradeLevels'));
  }

  /**
   * Show data in datatable.
   *
   */
  public function datatable()
  {
    $data = MinimalCompletenessCriteria::with(['subject', 'gradeLevel', 'semester'])
      ->orderBy('id', 'desc')
      ->get();
    return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('level', function ($query) {
        $config = optional(configuration())->type_school;
        if ($config == 1) {
          return 'Semester ' . $query->semester->number;
        } else {
          return $query->gradeLevel->name;
        }
      })
      ->addColumn('action', function ($query) {
        $updateDelete = checkPermission()->update_delete;
        $update = checkPermission()->update;
        $delete = checkPermission()->delete;
        $button = null;

        if ($updateDelete) {
          $button = '<a href="#" class="btn btn-success btn-sm btn-edit" title="Edit Data" id="' . $query->id . '"><i class="icon icon-pencil-square-o"></i></a>
                     <a href="#" class="btn btn-danger btn-sm" id="' . $query->id . '" title="Delete Data"><i class="icon icon-trash-o"></i></a>';
        } else if ($update) {
          $button = '<a href="#" class="btn btn-success btn-sm btn-edit" title="Edit Data" id="' . $query->id . '"><i class="icon icon-pencil-square-o"></i></a>';
        } else if ($delete) {
          $button = '<a href="#" class="btn btn-danger btn-sm btn-delete" id="' . $query->id . '" title="Delete Data"><i class="icon icon-trash-o"></i></a>';
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
   * @param MinimalCompletenessCriteriaRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(MinimalCompletenessCriteriaRequest $request)
  {
    $semester = $request->semester_id;
    $gradeLevel = $request->grade_level_id;
    $subject = $request->subject_id;
    $minimalCriteria = $request->minimal_criteria;
    $allSubject = $request->all_subject;
    $allGradeLevel = $request->all_grade_level;
    $schoolYear = activeSchoolYear()->id;
    $config = optional(configuration())->type_school;
    $insert = null;
    $data = [];

    /* check the school year is exist or not */
    if (is_null($schoolYear)) {
      return response()->json(['status' => 500, 'message' => 'Belum ada tahun ajar aktif']);
    }

    /* minimal criteria cannot be less than 0 and more than 100 */
    if ($minimalCriteria <= 0 || $minimalCriteria > 100) {
      return response()->json(['status' => 500, 'message' => 'KKM harus bernilai 1-100']);
    }

    /* check whether all subject are selected */
    if ($allSubject == 1) {
      if ($config == 1) {
        $subjects = Subject::where('semester_id', $semester)->get();
      } else {
        $subjects = Subject::all();
      }

      /* check subject data is exist or not */
      if (empty($subjects)) {
        return response()->json(['status' => 500, 'message' => 'Belum ada data ' . subjectName()]);
      }
    } else {
      $subjects = [(object)['id' => $subject]];
    }

    /* check whether all grade level or semester are selected (type school not for university) */
    if ($allGradeLevel == 1) {
      $levels = GradeLevel::all();
      if (empty($levels)) {
        return response()->json(['status' => 500, 'message' => 'Belum ada data tingkat kelas']);
      }
    } else {
      $levels = [(object)['id' => $gradeLevel]];
    }

    foreach ($subjects as $item) {
      if ($config == 1) {
        $data[] = [
          'semester_id' => $semester,
          'grade_level_id' => null,
          'subject_id' => $item->id,
          'school_year_id' => $schoolYear,
          'minimal_criteria' => $minimalCriteria,
          'created_by' => Auth::user()->employee_id,
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ];
      } else {
        foreach ($levels as $level) {
          $data[] = [
            'semester_id' => null,
            'grade_level_id' => $level->id,
            'subject_id' => $item->id,
            'school_year_id' => $schoolYear,
            'minimal_criteria' => $minimalCriteria,
            'created_by' => Auth::user()->employee_id,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
          ];
        }
      }
    }

    $insert = MinimalCompletenessCriteria::insert($data);

    if ($insert) {
      $json = ['status' => 200, 'message' => 'Data berhasil disimpan'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal disimpan'];
    }

    return response()->json($json);
  }

  /**
   * get subject
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getSubject(Request $request)
  {
    $semesterId = $request->semester_id;
    $rawSubjects = null;
    $data = [];

    if (!is_null($semesterId)) {
      $rawSubjects = Subject::with('minimalCriteria')
        ->where('semester_id', $semesterId)
        ->get();
    } else {
      if (optional(configuration())->type_school != 1) {
        $rawSubjects = Subject::with('minimalCriteria')->get();
      } else {
        $rawSubjects = collect([]);
      }
    }

    foreach ($rawSubjects as $subject) {
      if (is_null($subject->minimalCriteria)) {
        $data[] = $subject;
      }
    }

    $subjects = [];
    if (!empty($data)) {
      foreach ($data as $item) {
        /* check whether the subject already or not */
        $exists = MinimalCompletenessCriteria::where('subject_id', $item->id)->first();

        if (!$exists) {
          $subjects[] = $item;
        }
      }
    }

    return response()->json(['status' => 200, 'data' => $subjects]);
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
    $data = MinimalCompletenessCriteria::where('id', $id)->first();

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
   * @param MinimalCompletenessCriteriaRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(MinimalCompletenessCriteriaRequest $request)
  {
    $id = $request->id;
    $minimalCriteria = $request->minimal_criteria;

    $update = MinimalCompletenessCriteria::where('id', $id)->first()->update([
      'minimal_criteria' => $minimalCriteria,
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
    $delete = MinimalCompletenessCriteria::where('id', $id)->first();
    $delete->delete();

    if ($delete) {
      $json = ['status' => 200, 'message' => 'Data berhasil dihapus'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal dihapus'];
    }

    return response()->json($json);
  }
}
