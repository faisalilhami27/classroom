<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RemedialRequest extends FormRequest
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
      'select_question' => 'required',
      'start_date' => 'required',
      'end_date' => 'required',
      'start_time' => 'required',
      'end_time' => 'required',
      'select_student_category' => 'required',
      'student_id' => 'required',
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
      'select_question.required' => 'Pilih Soal tidak boleh kosong',
      'start_date.required' => 'Tanggal mulai tidak boleh kosong',
      'end_date.required' => 'Tanggal selesai tidak boleh kosong',
      'start_time.required' => 'Waktu mulai tidak boleh kosong',
      'end_time.required' => 'Waktu selesai tidak boleh kosong',
      'select_student_category.required' => 'Kategori tidak boleh kosong',
      'student_id.required' => 'Siswa tidak boleh kosong',
    ];
  }
}
