<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendStudentTaskRequest extends FormRequest
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
      'file' => 'required|mimes:zip,rar,docx,doc,pdf,xlsx,xls,ppt,pptx'
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
      'file.required' => 'File tidak boleh kosong',
      'file.mimes' => 'Format yang diterima zip, rar, docx, doc, pdf, xlsx, xls, ppt dan pptx'
    ];
  }
}
