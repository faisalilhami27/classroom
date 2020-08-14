<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SemesterRequest extends FormRequest
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
      'number' => 'required|regex:/^[0-9]*$/|max:5|min:1'
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
      'number.required' => 'Nomor semester tidak boleh kosong',
      'number.regex' => 'Nomor semester harus huruf',
      'number.min' => 'Nomor semester minimal 1 huruf',
      'number.max' => 'Nomor semester maximal 5 huruf',
    ];
  }
}
