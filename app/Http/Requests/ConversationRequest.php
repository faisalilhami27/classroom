<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConversationRequest extends FormRequest
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
      'file.*' => 'mimes:jpg,png,jpeg,pdf,zip,rar,xlsx,xls,doc,docx,ppt,pptx',
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
      'file.mimes' => 'Format yang diterima jpg, jpeg, png, pdf, zip, xlsx, xls, doc, docx, ppt, dan pptx',
    ];
  }
}
