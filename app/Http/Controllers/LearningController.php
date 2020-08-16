<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\StudentClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LearningController extends Controller
{
  /**
   * get material by class
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function getMaterialByClass(Request $request)
  {
    $classId = $request->class_id;
    $subjectId = $request->subject_id;
    $class = StudentClass::where('id', $classId)
      ->where('subject_Id', $subjectId)
      ->first();
    $materials = Material::where('employee_id', $class->employee_id)
      ->where('subject_id', $subjectId)
      ->orderBy('position', 'asc');

    return response()->json([
      'status' => 200,
      'all' => $materials->get(),
      'material' => $materials->paginate(1),
    ]);
  }
}
