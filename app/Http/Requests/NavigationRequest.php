<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NavigationRequest extends FormRequest
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
      'title' => 'required|max:100|regex:/^[a-zA-Z- ]*$/|min:3',
      'url' => 'required|regex:/^[a-zA-Z#-_.\/]*$/|max:50',
      'parent_id' => 'required',
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
      'title.required' => 'Nama menu tidak boleh kosong',
      'title.regex' => 'Nama menu hanya diperbolehkan huruf, strip dan spasi',
      'title.min' => 'Nama menu minimal 3 huruf',
      'title.max' => 'Nama menu maximal 100 huruf',
      'url.required' => 'URL tidak boleh kosong',
      'url.regex' => 'Format yang diperbolehkan huruf, titik, strip, underscore, garis miring dan pagar',
      'url.max' => 'Nama menu maximal 50 huruf',
      'parent_id.required' => 'Main Menu tidak boleh kosong',
    ];
  }
}
