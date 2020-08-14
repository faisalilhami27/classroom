<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VideoConferenceRequest extends FormRequest
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
      "first_name" => "required|regex:/^[a-zA-Z ]*$/|max:100",
      "last_name" => "regex:/^[a-zA-Z ]*$/|max:100",
      "email" => "required|email|max:100",
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
      'first_name.required' => 'First name tidak boleh kosong',
      'first_name.regex' => 'First name hanya diperbolehkan huruf',
      'last_name.regex' => 'Last name hanya diperbolehkan huruf',
      'email.required' => 'Email tidak boleh kosong',
      'email.email' => 'Format email tidak benar'
    ];
  }
}
