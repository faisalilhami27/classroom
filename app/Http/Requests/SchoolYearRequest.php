<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SchoolYearRequest extends FormRequest
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
      'early_year' => 'required|regex:/^[0-9]*$/|max:4|min:4',
      'end_year' => 'required|regex:/^[0-9]*$/|max:4|min:4',
      'semester' => 'required'
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
      'early_year.required' => 'Tahun awal tidak boleh kosong',
      'early_year.regex' => 'Tahun awal harus angka',
      'early_year.min' => 'Tahun awal minimal 4 angka',
      'early_year.max' => 'Tahun awal maximal 4 angka',
      'end_year.required' => 'Tahun akhir tidak boleh kosong',
      'end_year.regex' => 'Tahun akhir harus angka',
      'end_year.min' => 'Tahun akhir minimal 4 angka',
      'end_year.max' => 'Tahun akhir maximal 4 angka',
      'semester.required' => 'Semester tidak boleh kosong',
    ];
  }
}
