<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MajorRequest extends FormRequest
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
      'code' => 'required|regex:/^[a-zA-Z0-9-. ]*$/|max:10|min:2',
      'name' => 'required|regex:/^[a-zA-Z ]*$/|max:100|min:3'
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
      'code.required' => 'Kode tidak boleh kosong',
      'code.regex' => 'Kode harus huruf, angka, strip dan titik',
      'code.min' => 'Kode minimal 2 huruf',
      'code.max' => 'Kode maximal 10 huruf',
      'name.required' => 'Nama jurusan tidak boleh kosong',
      'name.regex' => 'Nama jurusan harus huruf',
      'name.min' => 'Nama jurusan minimal 3 huruf',
      'name.max' => 'Nama jurusan maximal 100 huruf',
    ];
  }
}
