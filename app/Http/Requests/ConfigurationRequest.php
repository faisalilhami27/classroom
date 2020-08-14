<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfigurationRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'school_name' => 'required|max:100|regex:/^[a-zA-Z0-9- ]*$/',
      'type_school' => 'required',
      'reset_password_employee' => 'required',
      'reset_password_student' => 'required',
      'school_logo' => 'mimes:png,jpg,jpeg'
    ];
  }

  /**
   * Get the error messages for the defined validation rules.
   *
   * @return array
   */
  public function messages()
  {
    return [
      'school_name.required' => 'Nama sekolah tidak boleh kosong',
      'school_name.regex' => 'Nama sekolah hanya diperbolehkan huruf, angka dan strip',
      'type_school.required' => 'Jenis sekolah tidak boleh kosong',
      'reset_password_employee.required' => 'Reset password Karyawan tidak boleh kosong',
      'reset_password_student.required' => 'Reset password Siswa tidak boleh kosong',
      'school_logo.mimes' => 'Format yang diizinkan png, jpg, dan jpeg'
    ];
  }
}
