<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubjectRequest extends FormRequest
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
        'code' => 'required|min:2|max:10|regex:/^[a-zA-Z0-9 ]*$/',
        'name' => 'required|min:3|max:100|regex:/^[a-zA-Z& ]*$/',
        'semester_id' => 'required',
        'major_id' => 'required',
      ];
    } else {
      return [
        'code' => 'required|min:2|max:10|regex:/^[a-zA-Z0-9 ]*$/',
        'name' => 'required|min:2|max:100|regex:/^[a-zA-Z ]*$/',
        'major_id' => 'required',
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
      'code.required' => 'Kode tidak boleh kosong',
      'code.regex' => 'Kode harus huruf atau angka',
      'code.min' => 'Kode minimal 2 huruf',
      'code.max' => 'Kode maximal 10 huruf',
      'name.required' => 'Nama tidak boleh kosong',
      'name.regex' => 'Nama harus alphabet dan simbol &',
      'name.min' => 'Nama minimal 3 huruf',
      'name.max' => 'Nama maximal 100 huruf',
      'semester_id.required' => 'Semester tidak boleh kosong',
      'major_id.required' => 'Jurusan tidak boleh kosong'
    ];
  }
}
