<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
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
      'title' => 'required|regex:/^[a-zA-Z0-9-., ]*$/|max:100|min:5',
      'point' => 'required|lte:100|integer|regex:/^[0-9]*$/',
      'file.*' => 'mimes:jpg,png,jpeg,pdf,zip,rar',
      'students' => 'required',
      'date' => 'required',
      'time' => 'required',
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
      'title.required' => 'Judul tidak boleh kosong',
      'student.required' => 'Siswa tidak boleh kosong',
      'date.required' => 'Tanggal tidak boleh kosong',
      'time.required' => 'Jam tidak boleh kosong',
      'title.regex' => 'Format yang diterima huruf, angka, titik, koma dan strip',
      'title.max' => 'Judul maksimal 100 karakter',
      'title.min' => 'Judul minimal 5 karakter',
      'point.required' => 'Point tidak boleh kosong',
      'point.integer' => 'Point harus bilangan bulat',
      'point.regex' => 'Format point harus angka',
      'point.lte' => 'Point harus kurang dari 100',
      'file.*.mimes' => 'Format yang diterima jpg, jpeg, png, pdf, zip dan rar',
    ];
  }
}
