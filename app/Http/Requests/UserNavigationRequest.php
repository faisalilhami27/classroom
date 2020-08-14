<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserNavigationRequest extends FormRequest
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
      'role_user_id' => 'required',
      'navigation_id' => 'required'
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
      'role_user_id.required' => 'Role tidak boleh kosong',
      'navigation_id.required' => 'Navigation tidak boleh kosong',
    ];
  }
}
