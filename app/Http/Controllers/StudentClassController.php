<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentClassRequest;
use App\Models\Configuration;
use App\Models\GradeLevel;
use App\Models\Major;
use App\Models\SchoolYear;
use App\Models\Semester;
use App\Models\StudentClass;
use App\Models\StudentClassTransaction;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class StudentClassController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index()
  {
    $title = 'Daftar Kelas';
    $semesters = Semester::all();
    $gradeLevels = GradeLevel::all();
    $subjects = Subject::all();
    $majors = Major::all();
    $config = optional(configuration())->type_school;

    /* check type school if 1 means university */
    if ($config == 1) {
      return view('backend.studentClass.univ', compact('title', 'semesters', 'subjects', 'majors'));
    } else {
      return view('backend.studentClass.school', compact('title', 'gradeLevels', 'subjects', 'majors'));
    }
  }

  /**
   * get subject data by semester
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getSubject(Request $request)
  {
    $semester = $request->semester_id;
    $major = $request->major_id;
    $data = collect([]);

    /* check semester null or not */
    if (!is_null($semester)) {
      $data = Subject::where('semester_id', $semester)
        ->where('major_id', $major)
        ->get();
    }

    if ($data) {
      $json = ['status' => 200, 'data' => $data];
    } else {
      $json = ['status' => 500, 'message' => 'Data tidak ditemukan'];
    }

    return response()->json($json);
  }

  /**
   * Show data in datatable.
   *
   */
  public function datatable()
  {
    $studentClass = StudentClass::with(['subject', 'major'])
      ->orderBy('id', 'desc');

    if (Session::get('role_id') == 3) {
      $studentClass->where('employee_id', Auth::user()->employee_id);
    }

    $data = $studentClass->get();
    return DataTables::of($data)
      ->addIndexColumn()
      ->addColumn('school_year', function ($query) {
        $schoolYear = SchoolYear::where('id', $query->school_year_id)->first();
        if (is_null($schoolYear)) {
          return "Belum ada tahun ajar aktif";
        } else {
          return $schoolYear->early_year . '/' . $schoolYear->end_year;
        }
      })
      ->addColumn('level', function ($query) {
        $level = null;
        $config = Configuration::first();
        if ($config->type_school == 1) {
          $semester = Semester::find($query->semester_id);
          $level = 'Semester ' . optional($semester)->number;
        } else {
          $gradeLevel = GradeLevel::find($query->grade_level_id);
          $level = optional($gradeLevel)->name;
        }
        return $level;
      })
      ->addColumn('action', function ($query) {
        $updateDelete = checkPermission()->update_delete;
        $update = checkPermission()->update;
        $delete = checkPermission()->delete;
        $button = null;

        if ($updateDelete) {
          $button = '<a href="#" class="btn btn-info btn-sm btn-view" title="Daftar Siswa" id="' . $query->id . '"><i class="icon icon-list"></i></a>
                     <a href="#" class="btn btn-success btn-sm btn-edit" title="Edit Data" id="' . $query->id . '"><i class="icon icon-pencil-square-o"></i></a>
                     <a href="#" class="btn btn-danger btn-sm btn-delete" id="' . $query->id . '" title="Delete Data"><i class="icon icon-trash-o"></i></a>';
        } else if ($update) {
          $button = '<a href="#" class="btn btn-info btn-sm btn-view" title="Daftar Siswa" id="' . $query->id . '"><i class="icon icon-list"></i></a>
                     <a href="#" class="btn btn-success btn-sm btn-edit" title="Edit Data" id="' . $query->id . '"><i class="icon icon-pencil-square-o"></i></a>';
        } else if ($delete) {
          $button = '<a href="#" class="btn btn-info btn-sm btn-view" title="Daftar Siswa" id="' . $query->id . '"><i class="icon icon-list"></i></a>
                     <a href="#" class="btn btn-danger btn-sm btn-delete" id="' . $query->id . '" title="Delete Data"><i class="icon icon-trash-o"></i></a>';
        } else {
          $button = '<a href="#" class="btn btn-info btn-sm btn-view" title="Daftar Siswa" id="' . $query->id . '"><i class="icon icon-list"></i></a>';
        }
        return $button;
      })
      ->make(true);
  }

  /**
   * Show student data in datatable.
   * @param Request $request
   * @return
   * @throws \Exception
   */
  public function datatableStudent(Request $request)
  {
    $classId = $request->class_id;
    $student = StudentClassTransaction::with(['student'])
      ->where('class_id', $classId)
      ->orderBy('id', 'desc')
      ->get();

    return DataTables::of($student)
      ->addIndexColumn()
      ->addColumn('checkbox', function ($query) {
        return '<label class="custom-control custom-control-danger custom-checkbox">
                  <input class="custom-control-input checkbox_student" type="checkbox" name="checkbox_student[]" value="' . $query->student->id . '">
                  <span class="custom-control-indicator"></span>
                </label>';
      })
      ->rawColumns(['checkbox'])
      ->make(true);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param StudentClassRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(StudentClassRequest $request)
  {
    $semester = $request->semester_id;
    $gradeLevel = $request->grade_level_id;
    $subject = $request->subject_id;
    $major = $request->major_id;
    $classOrder = $request->class_order;
    $subjectData = Subject::find($subject);
    $randomImage = $this->randomImage()[0];
    $randomColor = $this->randomColor();
    $checkDuplicate = StudentClass::where('class_order', $classOrder)
      ->where('subject_id', $subject)
      ->first();

    if (is_null(activeSchoolYear())) {
      return response()->json(['status' => 500, 'message' => 'Belum ada tahun ajar aktif']);
    }

    /* check Duplicate */
    if (!is_null($checkDuplicate)) {
      return response()->json(['status' => 500, 'message' => 'Kelas ' . $subjectData->name . ' untuk urutan kelas ' . $classOrder . ' sudah ada.']);
    }

    $insert = StudentClass::create([
      'code' => Str::random(7),
      'class_order' => strtoupper($classOrder),
      'semester_id' => (is_null($semester)) ? null : $semester,
      'image' => $randomImage,
      'color' => $randomColor,
      'grade_level_id' => (is_null($gradeLevel)) ? null : $gradeLevel,
      'major_id' => $major,
      'subject_id' => $subject,
      'school_year_id' => activeSchoolYear()->id,
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

  /** random images
   *
   */
  private function randomImage()
  {
    $filesInFolder = File::files('images');
    $data = [];
    foreach ($filesInFolder as $path) {
      $file = pathinfo($path);
      $data[] = $file['basename'];
    }
    return Arr::random($data, 1);
  }

  /** random color
   *
   */
  private function randomColor()
  {
    return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
  }

  /**
   * get class by user
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getClassByUser(Request $request)
  {
    $data = [];
    $userId = $request->user_id;
    $classes = null;

    if (Auth::guard('student')->check()) {
      $classes = StudentClassTransaction::with(['studentClass.subject', 'studentClass.schoolYear'])
        ->where('student_id', $userId)
        ->whereHas('studentClass', function ($query) {
          $query->where('school_year_id', optional(activeSchoolYear())->id);
        })
        ->get();

      foreach ($classes as $class) {
        $studentClass = $this->checkClassStudent($class);
        $data[] = [
          'id' => $class->studentClass->id,
          'subject_id' => $class->studentClass->subject_id,
          'subject' => $class->studentClass->subject->name,
          'color' => $class->studentClass->color,
          'class' => $studentClass,
          'school_year' => 'Tahun Ajaran ' . $class->studentClass->schoolYear->early_year . '/' . $class->studentClass->schoolYear->end_year,
          'image' => asset('images/' . $class->studentClass->image)
        ];
      }
    } else {
      $classes = StudentClass::with(['subject', 'schoolYear'])
        ->where('employee_id', $userId)
        ->where('school_year_id', activeSchoolYear()->id)
        ->get();

      foreach ($classes as $class) {
        $studentClass = $this->checkClassTeacher($class);
        $data[] = [
          'id' => $class->id,
          'subject_id' => $class->subject_id,
          'subject' => $class->subject->name,
          'color' => $class->color,
          'class' => $studentClass,
          'school_year' => 'Tahun Ajaran ' . $class->schoolYear->early_year . '/' . $class->schoolYear->end_year,
          'image' => asset('images/' . $class->image)
        ];
      }
    }

    if ($classes) {
      $json = ['status' => 200, 'list' => $data];
    } else {
      $json = ['status' => 500, 'message' => 'Data tidak ditemukan'];
    }

    return response()->json($json);
  }

  /**
   * check class student
   * @param $class
   * @return string
   */
  private function checkClassStudent($class)
  {
    if (optional(configuration())->type_school == 1) {
      $studentClass = "Kelas " . $class->studentClass->class_order;
    } else {
      if (optional(configuration())->type_school == 2) {
        $studentClass = "Kelas " . $class->studentClass->gradeLevel->name . ' - ' . $class->studentClass->major->code . ' - ' . $class->studentClass->class_order;
      } else {
        $studentClass = "Kelas " . $class->studentClass->gradeLevel->name . ' - ' . $class->studentClass->class_order;
      }
    }
    return $studentClass;
  }

  /**
   * check class teacher
   * @param $class
   * @return string
   */
  private function checkClassTeacher($class)
  {
    if (optional(configuration())->type_school == 1) {
      $studentClass = "Kelas " . $class->class_order;
    } else {
      if (optional(configuration())->type_school == 2) {
        $studentClass = "Kelas " . $class->gradeLevel->name . ' - ' . $class->major->code . ' - ' . $class->class_order;
      } else {
        $studentClass = "Kelas " . $class->gradeLevel->name . ' - ' . $class->class_order;
      }
    }
    return $studentClass;
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
    $data = StudentClass::find($id);
    $subjects = Subject::where('semester_id', optional($data)->semester_id)
      ->orWhere('semester_id', null)
      ->get();

    if ($data) {
      $json = ['status' => 200, 'data' => $data, 'subjects' => $subjects];
    } else {
      $json = ['status' => 500, 'message' => 'Data tidak ditemukan'];
    }

    return response()->json($json);
  }

  /**
   * get data class by id.
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getClassById(Request $request)
  {
    $id = $request->id;
    $data = StudentClass::with(['subject', 'schoolYear'])->where('id', $id)->first();

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
   * @param StudentClassRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(StudentClassRequest $request)
  {
    $id = $request->id;
    $semester = $request->semester_id;
    $gradeLevel = $request->grade_level_id;
    $subject = $request->subject_id;
    $major = $request->major_id;
    $classOrder = $request->class_order;

    $update = StudentClass::find($id)->update([
      'class_order' => strtoupper($classOrder),
      'semester_id' => (is_null($semester)) ? null : $semester,
      'grade_level_id' => (is_null($gradeLevel)) ? null : $gradeLevel,
      'subject_id' => $subject,
      'major_id' => $major,
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
   * student join class
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function studentJoinClass(Request $request)
  {
    $userId = $request->user_id;
    $classCode = $request->code;
    $class = StudentClass::where('code', $classCode)->first();
    $checkCode = StudentClass::where('code', $classCode)->get();
    $checkStudent = StudentClassTransaction::where('student_id', $userId)
      ->where('class_id', optional($class)->id)
      ->first();

    /* check whether class code is null or not */
    if (is_null($classCode)) {
      return response()->json(['status' => 500, 'message' => 'Maaf, kode tidak boleh kosong']);
    }

    /* check whether code is registered nor not */
    if ($checkCode->isEmpty()) {
      return response()->json(['status' => 500, 'message' => 'Maaf, kode kelas tidak terdaftar']);
    }

    /* check whether class code less than 7 */
    if (strlen($classCode) < 7) {
      return response()->json(['status' => 500, 'message' => 'Maaf, kode harus 7 karakter']);
    }

    /* check whether student has registered nor not in class */
    if (!is_null($checkStudent)) {
      return response()->json(['status' => 500, 'message' => 'Maaf, kamu sudah terdaftar di kelas ini']);
    } else {
      $checkStudentRegistered = StudentClassTransaction::where('student_id', $userId)->get();
      foreach ($checkStudentRegistered as $check) {
        $studentClass = StudentClass::where('id', $check->class_id)->first();
        $subject = Subject::find($studentClass->subject_id);
        if (optional($class)->subject_id == $subject->id) {
          return response()->json(['status' => 500, 'message' => 'Maaf, kamu sudah terdaftar di kelas ' . subjectName() . ' yang sama']);
        }
      }
    }

    /* check whether class is exist or not */
    if (is_null($class)) {
      return response()->json(['status' => 500, 'message' => 'Maaf, Kelas tidak ditemukan periksa kembali kode kelas']);
    }

    $insert = DB::transaction(function () use ($class, $userId) {
      $class->classTransaction()->create([
        'student_id' => $userId,
        'class_id' => $class->id
      ]);

      try {
        DB::commit();
        return true;
      } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['status' => 500, 'errors' => $e->getMessage()]);
      }
    }, 3);

    if ($insert) {
      $json = ['status' => 200, 'message' => 'Selamat kamu berhasil bergabung dengan kelas'];
    } else {
      $json = ['status' => 500, 'message' => 'Maaf, kamu gagal bergabung silahkan coba lagi'];
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
    $delete = StudentClass::find($id);
    $delete->delete();

    if ($delete) {
      $json = ['status' => 200, 'message' => 'Data berhasil dihapus'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal dihapus'];
    }

    return response()->json($json);
  }

  /**
   * delete student data from class
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroyStudent(Request $request)
  {
    $studentList = $request->student_list;
    $countStudent = count($studentList);
    $delete = null;

    foreach ($studentList as $student) {
      $delete = StudentClassTransaction::where('student_id', $student)->delete();
    }

    if ($delete) {
      $json = ['status' => 200, 'message' => $countStudent . ' siswa berhasil dihapus'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal dihapus'];
    }

    return response()->json($json);
  }
}
