<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfigurationRequest;
use App\Models\Configuration;
use App\Models\TypeSchool;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ConfigurationController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
   */
  public function index()
  {
    $title = 'Konfigurasi Aplikasi';
    $config = Configuration::first();
    $typeSchools = TypeSchool::all();
    return view('backend.configuration.index', compact('title', 'config', 'typeSchools'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param ConfigurationRequest $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(ConfigurationRequest $request)
  {
    $schoolName = htmlspecialchars($request->school_name);
    $typeSchool = $request->type_school;
    $resetPasswordEmployee = htmlspecialchars($request->reset_password_employee);
    $resetPasswordStudent = htmlspecialchars($request->reset_password_student);
    $schoolLogo = $request->file('school_logo');
    $configuration = Configuration::first();
    $logo = (is_null($schoolLogo)) ? null : $schoolLogo->store('school_logo', 'public');
    $store = null;

    // check if the configuration is still empty
    if (empty($configuration)) {
      $store = Configuration::create([
        'school_name' => $schoolName,
        'type_school' => $typeSchool,
        'reset_password_employee' => $resetPasswordEmployee,
        'reset_password_student' => $resetPasswordStudent,
        'school_logo' => $logo
      ]);
    } else {
      // check if logo null
     if (is_null($logo)) {
       $configuration->school_name = $schoolName;
       $configuration->type_school = $typeSchool;
       $configuration->reset_password_employee = $resetPasswordEmployee;
       $configuration->reset_password_student = $resetPasswordStudent;
     } else {
       Storage::disk('public')->delete($configuration->school_logo);
       $configuration->school_name = $schoolName;
       $configuration->type_school = $typeSchool;
       $configuration->reset_password_employee = $resetPasswordEmployee;
       $configuration->reset_password_student = $resetPasswordStudent;
       $configuration->school_logo = $logo;
     }
      $store = $configuration->save();
    }

    if ($store) {
      $json = ['status' => 200, 'message' => 'Data berhasil disimpan'];
    } else {
      $json = ['status' => 500, 'message' => 'Data gagal disimpan'];
    }

    return response()->json($json);
  }
}
