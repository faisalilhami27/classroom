<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionBankImportRequest extends FormRequest
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
    if (optional(configuration())->type_school == 1) {
      return [
        'file_import' => 'required|mimes:xlsx,xls',
        'subject_id_import' => 'required',
        'semester_id_import' => 'required',
        'school_year_id_import' => 'required'
      ];
    } else {
      return [
        'file_import' => 'required|mimes:xlsx,xls',
        'subject_id_import' => 'required',
        'grade_level_id_import' => 'required',
        'school_year_id_import' => 'required'
      ];
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
      'file_import.required' => 'File tidak boleh kosong',
      'file_import.mimes' => 'File yang diterima xlsx atau xls',
      'grade_level_id_import.required' => 'Tingkat kelas tidak boleh kosong',
      'semester_id_import.required' => 'Semester tidak boleh kosong',
      'subject_id_import.required' => subjectName() . ' tidak boleh kosong',
      'school_year_id_import.required' => 'Tahun ajar tidak boleh kosong'
    ];
  }
}
