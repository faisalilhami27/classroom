<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
      "employee_identity_number" => "required|regex:/^[0-9]*$/|max:16|min:16",
      "first_name" => "required|regex:/^[a-zA-Z ]*$/|max:100",
      "email" => "required|email|max:100",
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
      'employee_identity_number.required' => 'NIP tidak boleh kosong',
      'employee_identity_number.regex' => 'NIP harus angka',
      'employee_identity_number.min' => 'NIP harus 16 angka',
      'first_name.required' => 'First name tidak boleh kosong',
      'first_name.regex' => 'First name hanya diperbolehkan huruf',
      'email.required' => 'Email tidak boleh kosong',
      'email.email' => 'Format email tidak benar',
      'phone_number.required' => 'Nomor Handphone tidak boleh kosong',
      'phone_number.regex' => 'Nomor Handphone harus angka',
      'phone_number.max' => 'Nomor Handphone maksimal 15 angka',
      'phone_number.min' => 'Nomor Handphone minimal 10 angka',
    ];
  }
}
