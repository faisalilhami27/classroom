<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserEmployeeRequest extends FormRequest
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
      "employee_id" => "required",
      "username" => "required|regex:/^[a-zA-Z0-9_. ]*$/|max:20|min:3",
      "password" => "required|min:8",
      "role_id" => "required|",
      "status" => "required",
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
      'employee_id.required' => 'Karyawan tidak boleh kosong',
      'username.required' => 'Username tidak boleh kosong',
      'username.regex' => 'Format yang diperbolehkan huruf, angka, titik, dan underscore',
      'username.max' => 'Username maksimal 20 karakter',
      'username.min' => 'Username minimal 3 karakter',
      'password.required' => 'Password tidak boleh kosong',
      'password.min' => 'Password minimal 8 karakter',
      'role_id.required' => 'Role tidak boleh kosong',
      'status.required' => 'Status tidak boleh kosong',
    ];
  }
}
