<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaterialRequest extends FormRequest
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
      return [
        "semester_id" => "required",
        "subject_id" => "required",
        "major_id" => "required",
        "title" => "required|max:100",
        "position" => "required|regex:/^[0-9]*$/",
        "detail_material" => "required",
        "module" => "mimes:pdf,ppt,pptx,doc,docx",
        "archive" => "mimes:zip,rar",
      ];
    } else {
      return [
        "grade_level_id" => "required",
        "subject_id" => "required",
        "position" => "required|regex:/^[0-9]*$/",
        "detail_material" => "required",
        "module" => "mimes:pdf,ppt,pptx,doc,docx",
        "archive" => "mimes:zip,rar",
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
      'semester_id.required' => 'Semester tidak boleh kosong',
      'grade_level_id.required' => 'Tingkat kelas tidak boleh kosong',
      'major_id.required' => 'Jurusan tidak boleh kosong',
      'subject_id.required' => subjectName() . ' tidak boleh kosong',
      'position.required' => 'Urutan materi tidak boleh kosong',
      'position.regex' => 'Urutan materi harus angka',
      'title.required' => 'Judul materi tidak boleh kosong',
      'detail_material.required' => 'Penjelasan materi tidak boleh kosong',
      'module.mimes' => 'Format materi yang diterima pdf, ppt, pptx, doc, dan docx',
      'archive.mimes' => 'Format source code yang diterima zip dan rar',
    ];
  }
}
