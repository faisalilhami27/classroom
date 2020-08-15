<?php

namespace App\Http\Controllers;

use App\Exports\QuestionBankExport;
use App\Http\Requests\AnswerQuestionRequest;
use App\Http\Requests\QuestionBankImportRequest;
use App\Http\Requests\QuestionBankRequest;
use App\Http\Requests\QuestionBankUpdateRequest;
use App\Imports\QuestionBankImport;
use App\Models\AnswerKey;
use App\Models\GradeLevel;
use App\Models\Major;
use App\Models\QuestionBank;
use App\Models\SchoolYear;
use App\Models\Semester;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class QuestionBankController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index()
  {
    $title = 'Daftar Bank Soal';
    $subjects = Subject::all();
    $semesters = Semester::all();
    $gradeLevels = GradeLevel::all();
    $schoolYears = SchoolYear::all();
    $majors = Major::all();
    return view('backend.cbt.question', compact('title', 'subjects', 'semesters', 'gradeLevels', 'schoolYears', 'majors'));
  }

  /**
   * import data question bank from excel
   * @param QuestionBankImportRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function import(QuestionBankImportRequest $request)
  {
    Session::put('success', 0);
    Session::put('failed', 0);

    $file = $request->file('file_import');
    $semesterId = $request->semester_id_import;
    $gradeLevelId = $request->grade_level_id_import;
    $subjectId = $request->subject_id_import;
    $schoolYearId = $request->school_year_id_import;

    $import = Excel::import(new QuestionBankImport($semesterId, $gradeLevelId, $subjectId, $schoolYearId), $file);
    $failed = Session::get('failed');

    if ($failed == 0) {
      $message = "Soal yang berhasil di import = " . Session::get('success');
    } else {
      $message = "Soal yang success di import = " . Session::get('berhasil') . " dan soal yang gagal di import = " . Session::get('failed');
    }

    if ($import) {
      return response()->json(['status' => 200, 'message' => $message]);
    } else {
      return response()->json(['status' => 500, 'message' => 'Data gagal diimport']);
    }
  }

  /**
   * link for export question bank
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function linkExport(Request $request)
  {
    $level = $request->level_filter;
    $subject = $request->subject_filter;
    return response()->json(['status' => 200, 'level' => $level, 'subject' => $subject]);
  }

  /**
   * export data question bank to excel
   * @param $level
   * @param $subject
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   */
  public function export($level, $subject)
  {
    if ($subject == 'all') {
      $fileName = 'Seluruh Bank Soal';
    } else {
      $fileName = 'Bank Soal ' . $subject;
    }

    return Excel::download(new QuestionBankExport($level, $subject), $fileName . '.xlsx');
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
    $data = QuestionBank::with(['subject', 'semester', 'gradeLevel'])
      ->orderBy('id', 'desc');
    $level = $request->level;
    $subject = $request->subject;

    /* filter by on semester or grade level */
    if ($level != 'all') {
      if (optional(configuration())->type_school == 1) {
        $data->where('semester_id', $level);
      } else {
        $data->where('grade_level_id', $level);
      }
    }

    /* filter by on subject */
    if ($subject != 'all') {
      $data->where('subject_id', $subject);
    }

    $question = $data->get();
    return DataTables::of($question)
      ->addIndexColumn()
      ->addColumn('level', function ($query) {
        if (configuration()->type_school == 1) {
          return optional($query->semester)->number;
        } else {
          return optional($query->gradeLevel)->name;
        }
      })
      ->addColumn('type_question', function ($query) {
        $type = null;

        if ($query->type_question == 1) {
          $type = '<span class="badge badge-primary">Text</span>';
        } else if ($query->type_question == 2) {
          $type = '<span class="badge badge-info">Audio</span>';
        } else {
          $type = '<span class="badge badge-success">Video</span>';
        }

        return $type;
      })
      ->addColumn('select_answer_choice', function ($query) {
        $answerQuestion = AnswerKey::where('question_id', $query->id)
          ->where('key', 1)
          ->first();

        if (!is_null($answerQuestion)) {
          $checkAnswer = "<center><span class='text-success'><i class='icon icon-check'></i></span></center>";
        } else {
          $checkAnswer = "<center><span class='text-danger'><i class='icon icon-times'></span></i></center>";
        }

        return $checkAnswer;
      })
      ->addColumn('action', function ($query) {
        $updateDelete = checkPermission()->update_delete;
        $update = checkPermission()->update;
        $delete = checkPermission()->delete;
        $button = null;

        if ($updateDelete) {
          $button = '<a href="#" class="btn btn-success btn-sm btn-edit" title="Edit Data" id="' . $query->id . '" onclick="editData(' . $query->id . ')"><i class="icon icon-pencil-square-o"></i></a>
                     <a href="#" class="btn btn-info btn-sm" id="' . $query->id . '" onclick="setAnswer(' . $query->id . ')" title="Set Jawaban"><i class="icon icon-list"></i></a>
                     <a href="#" class="btn btn-danger btn-sm" id="' . $query->id . '" onclick="deleteData(' . $query->id . ')" title="Delete Data"><i class="icon icon-trash-o"></i></a>';
        } else if ($update) {
          $button = '<a href="#" class="btn btn-success btn-sm btn-edit" title="Edit Data" id="' . $query->id . '" onclick="editData(' . $query->id . ')"><i class="icon icon-pencil-square-o"></i></a>
                     <a href="#" class="btn btn-info btn-sm" id="' . $query->id . '" onclick="setAnswer(' . $query->id . ')" title="Set Jawaban"><i class="icon icon-list"></i></a>';
        } else if ($delete) {
          $button = '<a href="#" class="btn btn-danger btn-sm" id="' . $query->id . '" onclick="deleteData(' . $query->id . ')" title="Delete Data"><i class="icon icon-trash-o"></i></a>
                     <a href="#" class="btn btn-info btn-sm" id="' . $query->id . '" onclick="setAnswer(' . $query->id . ')" title="Set Jawaban"><i class="icon icon-list"></i></a>';
        } else {
          $button = '<a href="#" class="btn btn-info btn-sm" id="' . $query->id . '" onclick="setAnswer(' . $query->id . ')" title="Set Jawaban"><i class="icon icon-list"></i></a>';
        }
        return $button;
      })
      ->rawColumns(['level', 'select_answer_choice', 'action', 'type_question'])
      ->make(true);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param QuestionBankRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(QuestionBankRequest $request)
  {
    $semesterId = $request->semester_id;
    $gradeLevelId = $request->grade_level_id;
    $subjectId = $request->subject_id;
    $majorId = $request->major_id;
    $schoolYearId = $request->school_year_id;
    $typeQuestion = $request->type_question;
    $questionName = htmlspecialchars($request->question_name);
    $questionReplace = trim(preg_replace('/\s\s+/', ' ', $questionName));
    $document = $request->file('document');
    $insert = null;

    if (!empty($document)) {
      $filePath = null;
      $fileName = time() . '.' . $document->getClientOriginalExtension();
      if ($request->hasFile('document')) {
        if ($document->isValid()) {
          /* check if type question is audio */
          if ($typeQuestion == 2) {
            $filePath = $document->storeAs(
              'uploads/question_bank/audio', $fileName
            );
          } else if ($typeQuestion == 3) {
            $filePath = $document->storeAs(
              'uploads/question_bank/video', $fileName
            );
          }

          if ($filePath) {
            $insert = QuestionBank::create([
              'semester_id' => $semesterId,
              'grade_level_id' => $gradeLevelId,
              'subject_id' => $subjectId,
              'major_id' => $majorId,
              'school_year_id' => $schoolYearId,
              'employee_id' => Auth::user()->employee_id,
              'type_question' => $typeQuestion,
              'document' => $filePath,
              'extension' => $document->getClientOriginalExtension(),
              'question_name' => $questionReplace,
              'created_by' => Auth::user()->employee_id
            ]);
          }
        }
      }
    } else {
      $insert = QuestionBank::create([
        'major_id' => $majorId,
        'semester_id' => $semesterId,
        'grade_level_id' => $gradeLevelId,
        'subject_id' => $subjectId,
        'school_year_id' => $schoolYearId,
        'employee_id' => Auth::user()->employee_id,
        'type_question' => $typeQuestion,
        'question_name' => $questionReplace,
        'created_by' => Auth::user()->employee_id
      ]);
    }

    if ($insert) {
      $json = ['status' => 200, 'message' => 'Data berhasil disimpan'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal disimpan'];
    }

    return response()->json($json);
  }

  /**
   * insert answer question
   * @param AnswerQuestionRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function storeAnswerQuestion(AnswerQuestionRequest $request)
  {
    $questionId = $request->question_id;
    $answerChoice = $request->choice;
    $employeeId = Auth::user()->employee_id;

    if (empty($answerChoice)) {
      return response()->json(['status' => 500, 'message' => "Maaf, anda harus memilih satu jawaban."]);
    }

    $stored = 0;
    DB::transaction(function () use ($questionId, $answerChoice, $employeeId, $request, &$stored) {
      for ($i = 1; $i <= 5; $i++) {
        $answer = $request->input("answer" . $i);
        $answerId = $request->input("answer_id" . $i);

        /* check if answer id is empty */
        if (empty($answerId)) {
          if ($answerChoice == $i) {
            AnswerKey::create([
              'answer_name' => $answer,
              'key' => 1,
              'question_id' => $questionId,
              'employee_id' => $employeeId,
              'created_by' => Auth::user()->employee_id
            ]);
          } else {
            AnswerKey::create([
              'answer_name' => $answer,
              'key' => 0,
              'question_id' => $questionId,
              'employee_id' => $employeeId,
              'created_by' => Auth::user()->employee_id
            ]);
          }
        } else {
          if ($answerChoice == $i) {
            AnswerKey::find($answerId)->update([
              'answer_name' => $answer,
              'key' => 1,
              'question_id' => $questionId,
              'employee_id' => $employeeId,
              'last_updated_by' => Auth::user()->employee_id
            ]);
          } else {
            AnswerKey::find($answerId)->update([
              'answer_name' => $answer,
              'key' => 0,
              'question_id' => $questionId,
              'employee_id' => $employeeId,
              'last_updated_by' => Auth::user()->employee_id
            ]);
          }
        }
      }
      $stored++;
    }, 3);

    if ($stored > 0) {
      $json = ['status' => 200, 'message' => 'Data berhasil disimpan'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal disimpan'];
    }
    return response()->json($json);
  }

  /**
   * upload image question if using images
   * @param Request $request
   * @return string
   */
  public function uploadImageQuestion(Request $request)
  {
    $funcNum = $request->input('CKEditorFuncNum');
    $message = null;
    $url = null;
    if ($request->hasFile('upload')) {
      if ($request->file('upload')->isValid()) {
        $image = $request->file('upload');
        $fileName = $image->getClientOriginalName();
        $image->storeAs('uploads/question_bank/images', $fileName);
        $url = url('storage/uploads/question_bank/images/' . $fileName);
      } else {
        $message = 'Terjadi kesalahan saat mengunggah file.';
      }
    } else {
      $message = 'Tidak ada file yang diunggah.';
    }

    @header('Content-type: text/html; charset=utf-8');
    return "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message')</script>";
  }

  /**
   * check the Answer from question
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function checkAnswer(Request $request)
  {
    $id = $request->id;
    $data = QuestionBank::where('id', $id)->first();

    /* check the document is exist or not */
    if (!is_null($data->document)) {
      $fileExist = Storage::exists($data->document);
      if (!$fileExist) {
        $data = (object)['id' => $data->id, 'document' => 'Tidak ada'];
      }
    }

    $getAnswer = AnswerKey::where('question_id', $id)->get();
    $convertQuestion = htmlspecialchars($data->question_name);
    $questionReplace = trim(preg_replace('/\s\s+/', ' ', $convertQuestion));

    return response()->json(['status' => 200, 'data' => $data, 'answer' => $getAnswer, 'question' => $questionReplace]);
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
    $data = QuestionBank::find($id);
    $convertQuestion = htmlspecialchars($data->question_name);
    $questionReplace = trim(preg_replace('/\s\s+/', ' ', $convertQuestion));
    $subjects = Subject::where('semester_id', $data->semester_id)
      ->orWhere('semester_id', null)
      ->get();

    if ($data) {
      $json = ['status' => 200, 'data' => $data, 'subject' => $subjects, 'question' => $questionReplace];
    } else {
      $json = ['status' => 500, 'message' => 'Data tidak ditemukan'];
    }

    return response()->json($json);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param QuestionBankRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(QuestionBankUpdateRequest $request)
  {
    $id = $request->id;
    $semesterId = $request->semester_id;
    $gradeLevelId = $request->grade_level_id;
    $subjectId = $request->subject_id;
    $schoolYearId = $request->school_year_id;
    $typeQuestion = $request->type_question;
    $questionName = htmlspecialchars($request->question_name);
    $questionReplace = trim(preg_replace('/\s\s+/', ' ', $questionName));
    $document = $request->file('document');
    $data = QuestionBank::find($id);
    $update = null;

    if (!empty($document)) {
      $filePath = null;
      $fileName = time() . '.' . $document->getClientOriginalExtension();
      if ($request->hasFile('document')) {
        if ($document->isValid()) {
          Storage::disk('public')->delete($data->document);

          /* check if type question is audio or video */
          if ($typeQuestion == 2) {
            $filePath = $document->storeAs(
              'uploads/question_bank/audio', $fileName
            );
          } else if ($typeQuestion == 3) {
            $filePath = $document->storeAs(
              'uploads/question_bank/video', $fileName
            );
          }

          if ($filePath) {
            $update = $data->update([
              'semester_id' => $semesterId,
              'grade_level_id' => $gradeLevelId,
              'subject_id' => $subjectId,
              'major_id' => $majorId,
              'school_year_id' => $schoolYearId,
              'type_question' => $typeQuestion,
              'document' => $filePath,
              'extension' => $document->getClientOriginalExtension(),
              'question_name' => $questionReplace,
              'last_updated_by' => Auth::user()->employee_id
            ]);
          }
        }
      }
    } else {
      $update = $data->update([
        'major_id' => $majorId,
        'semester_id' => $semesterId,
        'grade_level_id' => $gradeLevelId,
        'subject_id' => $subjectId,
        'school_year_id' => $schoolYearId,
        'type_question' => $typeQuestion,
        'question_name' => $questionReplace,
        'last_updated_by' => Auth::user()->employee_id
      ]);
    }

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
    $delete = QuestionBank::find($id);
    $delete->answerKey()->delete();
    $delete->delete();

    if ($delete) {
      $json = ['status' => 200, 'message' => 'Data berhasil dihapus'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal dihapus'];
    }

    return response()->json($json);
  }
}
