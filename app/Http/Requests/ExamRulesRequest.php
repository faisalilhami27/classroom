<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExamRulesRequest extends FormRequest
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
      'text' => 'required',
      'name' => 'required|regex:/^[a-zA-Z0-9 ]*$/'
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
      'text.required' => 'Text tidak boleh kosong',
      'name.required' => 'Jenis ujian tidak boleh kosong',
      'name.regex' => 'Format yang diterima huruf dan angka',
    ];
  }
}
