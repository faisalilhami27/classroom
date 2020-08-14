<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest
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
      "student_identity_number" => "required|regex:/^[0-9]*$/|min:9",
      "name" => "required|regex:/^[a-zA-Z ]*$/|max:70",
      "email" => "required|email|max:70",
      "phone_number" => "required|regex:/^[0-9]*$/|max:15|min:10",
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
      'student_identity_number.regex' => 'NIPD harus angka',
      'student_identity_number.min' => 'NIPD harus minimal 9 angka',
      'name.required' => 'Nama tidak boleh kosong',
      'name.regex' => 'Nama hanya diperbolehkan huruf',
      'email.required' => 'Email tidak boleh kosong',
      'email.email' => 'Format email tidak benar',
      'phone_number.required' => 'Nomor Handphone tidak boleh kosong',
      'phone_number.regex' => 'Nomor Handphone harus angka',
      'phone_number.max' => 'Nomor Handphone maksimal 15 angka',
      'phone_number.min' => 'Nomor Handphone minimal 10 angka',
    ];
  }
}
