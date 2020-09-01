<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
      'password' => 'required|min:8|max:12',
      'password_confirmation' => 'required|min:8|max:12',
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
      'password.required' => 'Password tidak boleh kosong',
      'password.min' => 'Password minimal 8 karakter',
      'password.max' => 'Password maksimal 12 karakter',
      'password_confirmation.required' => 'Konfirmasi Password tidak boleh kosong',
      'password_confirmation.min' => 'Konfirmasi Password minimal 8 karakter',
      'password_confirmation.max' => 'Konfirmasi Password maksimal 12 karakter',
    ];
  }
}
