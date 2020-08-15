<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentClassRequest extends FormRequest
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
    if ($config == 1) {
      return [
        'class_order' => 'required|min:1|max:5|regex:/^[a-zA-Z0-9]*$/',
        'semester_id' => 'required',
        'subject_id' => 'required',
        'major_id' => 'required'
      ];
    } else {
     if ($config == 2) {
       return [
         'class_order' => 'required|min:1|max:5|regex:/^[a-zA-Z0-9]*$/',
         'grade_level_id' => 'required',
         'subject_id' => 'required',
         'major_id' => 'required'
       ];
     } else {
       return [
         'class_order' => 'required|min:1|max:5|regex:/^[a-zA-Z0-9]*$/',
         'grade_level_id' => 'required',
         'subject_id' => 'required'
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
    $config = (optional(configuration())->type_school == 1) ? 'Mata Kuliah' : 'Mata Pelajaran';
    return [
      'class_order.required' => 'Urutan kelas tidak boleh kosong',
      'class_order.regex' => 'Urutan kelas harus huruf atau angka',
      'class_order.min' => 'Urutan kelas minimal 1 huruf',
      'class_order.max' => 'Urutan kelas maximal 5 huruf',
      'semester_id.required' => 'Semester tidak boleh kosong',
      'grade_level_id.required' => 'Tingkat kelas tidak boleh kosong',
      'major_id.required' => 'Jurusan tidak boleh kosong',
      'subject_id.required' => $config . ' tidak boleh kosong',
    ];
  }
}
