<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MinimalCompletenessCriteriaRequest extends FormRequest
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
    $config = optional(configuration())->type_school;
    if (request('all_subject') == 1 || request('all_grade_level') == 1) {
      if ($config == 1) {
        return [
          'minimal_criteria' => 'required|regex:/^[0-9]*$/|min:1|max:5',
        ];
      } else {
        return [
          'minimal_criteria' => 'required|regex:/^[0-9]*$/|min:1|max:5',
        ];
      }
    } else {
      if ($config == 1) {
        return [
          'subject_id' => 'required',
          'semester_id' => 'required',
          'minimal_criteria' => 'required|regex:/^[0-9]*$/|min:1|max:5',
        ];
      } else {
        return [
          'subject_id' => 'required',
          'grade_level_id' => 'required',
          'minimal_criteria' => 'required|regex:/^[0-9]*$/|min:1|max:5',
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
      'subject_id.required' => 'Mata pelajaran tidak boleh kosong',
      'grade_level_id.required' => 'Tingkat kelas tidak boleh kosong',
      'semester_id.required' => 'Semester tidak boleh kosong',
      'minimal_criteria.required' => 'KKM tidak boleh kosong',
      'minimal_criteria.regex' => 'KKM harus diisi angka',
    ];
  }
}
