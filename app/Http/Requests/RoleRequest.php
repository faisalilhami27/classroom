<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
      'name' => 'required|max:30|regex:/^[a-zA-Z ]*$/|min:3'
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
      'name.required' => 'Nama role tidak boleh kosong',
      'name.regex' => 'Nama role harus huruf',
      'name.min' => 'Nama role minimal 3 huruf',
      'name.max' => 'Nama role maximal 30 huruf',
    ];
  }

}
