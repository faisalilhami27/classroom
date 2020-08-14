<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GradeLevelRequest extends FormRequest
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
      'name' => 'required|regex:/^[a-zA-Z ]*$/|max:5|min:1'
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
      'name.required' => 'Nama tingkat kelas tidak boleh kosong',
      'name.regex' => 'Nama tingkat kelas harus huruf',
      'name.min' => 'Nama tingkat kelas minimal 1 huruf',
      'name.max' => 'Nama tingkat kelas maximal 5 huruf',
    ];
  }
}
