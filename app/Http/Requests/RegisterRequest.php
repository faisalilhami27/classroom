<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
      "student_identity_number" => "required|regex:/^[0-9]*$/|max:20|min:9",
      "name" => "required|regex:/^[a-zA-Z ]*$/|max:70",
      "email" => "required|email|max:70",
      "username_register" => "required|max:20",
      "phone_number" => "required|regex:/^[0-9]*$/|max:15|min:10",
      "password_register" => "required|min:8|max:30",
      "password_confirmation" => "required|min:8|max:30"
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
      'student_identity_number.required' => 'NIPD tidak boleh kosong',
      'student_identity_number.numeric' => 'NIPD harus angka',
      'student_identity_number.min' => 'NIPD minimal sembilan angka',
      'name.required' => 'Nama tidak boleh kosong',
      'name.regex' => 'Nama hanya diperbolehkan huruf',
      'email.required' => 'Email tidak boleh kosong',
      'email.email' => 'Format email tidak benar',
      'username_register.required' => 'Username tidak boleh kosong',
      'phone_number.required' => 'Nomor Hp tidak boleh kosong',
      'phone_number.numeric' => 'Nomor Hp harus angka',
      'phone_number.max' => 'Nomor Hp maksimal 15 angka',
      'phone_number.min' => 'Nomor Hp minimal 10 angka',
      'password_register.required' => 'Password tidak boleh kosong',
      'password_register.min' => 'Password minimal 8 karakter',
      'password_confirmation.required' => 'Konfirmasi Password tidak boleh kosong',
      'password_confirmation.min' => 'Konfirmasi Password minimal 8 karakter',
    ];
  }
}
