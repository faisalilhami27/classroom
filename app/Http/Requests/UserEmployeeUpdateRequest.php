<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserEmployeeUpdateRequest extends FormRequest
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
      "username" => "required|regex:/^[a-zA-Z0-9_. ]*$/|max:20|min:3",
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
      'username.required' => 'Username tidak boleh kosong',
      'username.regex' => 'Format yang diperbolehkan huruf, angka, titik, dan underscore',
      'username.max' => 'Username maksimal 20 karakter',
      'username.min' => 'Username minimal 3 karakter',
      'role_id.required' => 'Role tidak boleh kosong',
      'status.required' => 'Status tidak boleh kosong',
    ];
  }
}
