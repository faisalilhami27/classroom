<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionBankUpdateRequest extends FormRequest
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
    if (configuration()->type_school == 1) {
      if (request('type_question') != 1) {
        return [
          "semester_id" => "required",
          "subject_id" => "required",
          "school_year_id" => "required",
          "type_question" => "required",
          "question_name" => "required",
        ];
      } else {
        return [
          "semester_id" => "required",
          "subject_id" => "required",
          "school_year_id" => "required",
          "type_question" => "required",
          "question_name" => "required",
        ];
      }
    } else {
      if (request('type_question') != 1) {
        return [
          "grade_level_id" => "required",
          "subject_id" => "required",
          "school_year_id" => "required",
          "type_question" => "required",
          "question_name" => "required",
        ];
      } else {
        return [
          "semester_id" => "required",
          "subject_id" => "required",
          "school_year_id" => "required",
          "type_question" => "required",
          "question_name" => "required",
        ];
      }
    }
  }

  /**
   * Get the error messages for the defined validation rules.
   *
   * @return array
   */
  public function messages()
  {
    return [
      'semester_id.required' => 'Semester tidak boleh kosong',
      'grade_level_id.required' => 'Tingkat kelas tidak boleh kosong',
      'subject_id.required' => subjectName() . ' tidak boleh kosong',
      'school_year_id.required' => 'Tahun ajar tidak boleh kosong',
      'type_question.required' => 'Jenis soal tidak boleh kosong',
      'document.required' => 'File tidak boleh kosong',
      'question_name.required' => 'Pertanyaan tidak boleh kosong',
    ];
  }
}
