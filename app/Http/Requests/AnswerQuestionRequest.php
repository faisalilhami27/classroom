<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnswerQuestionRequest extends FormRequest
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
      "answer1" => "required",
      "answer2" => "required",
      "answer3" => "required",
      "answer4" => "required",
      "answer5" => "required",
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
      'answer1.required' => 'Jawaban 1 tidak boleh kosong',
      'answer2.required' => 'Jawaban 2 tidak boleh kosong',
      'answer3.required' => 'Jawaban 3 tidak boleh kosong',
      'answer4.required' => 'Jawaban 4 tidak boleh kosong',
      'answer5.required' => 'Jawaban 5 tidak boleh kosong',
    ];
  }
}
