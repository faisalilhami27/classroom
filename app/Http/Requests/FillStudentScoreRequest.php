<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FillStudentScoreRequest extends FormRequest
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
      'score' => 'required|regex:/^[0-9]*$/|lte:100'
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
      'grade.required' => 'Nilai tidak boleh kosong',
      'grade.regex' => 'Nilai harus berupa angka',
      'grade.lte' => 'Nilai tidak boleh lebih dari 100',
    ];
  }
}
