<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubjectImportRequest extends FormRequest
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
    if (optional(configuration())->type_school == 1 || optional(configuration())->type_school == 2) {
      return [
        'file_import' => 'required|mimes:xlsx,xls',
        'major_id_import' => 'required',
      ];
    } else {
      return [
        'file_import' => 'required|mimes:xlsx,xls',
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
      'major_id_import.required' => 'Jurusan tidak boleh kosong',
    ];
  }
}
