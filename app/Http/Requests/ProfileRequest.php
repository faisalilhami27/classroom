<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
      'name' => 'required|regex:/^[a-zA-Z- ]*$/|min:3',
      'username' => 'required|min:5',
      'email' => 'required|email',
      'phone_number' => 'required|regex:/^[0-9]*$/'
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
      'name.required' => 'Nama tidak boleh kosong',
      'name.regex' => 'Nama hanya diperbolehkan huruf dan spasi',
      'phone_number.required' => 'Nomor handphone tidak boleh kosong',
      'phone_number.regex' => 'Nomor handphone diperbolehkan angka',
      'name.min' => 'Nama minimal 3 huruf',
      'username.required' => 'Username tidak boleh kosong',
      'username.min' => 'Username minimal 5 karakter',
      'email.required' => 'Email tidak boleh kosong',
      'email.email' => 'Format email salah',
    ];
  }
}
