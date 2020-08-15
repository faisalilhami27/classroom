<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManageExamRequest extends FormRequest
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
    if (configuration()->type_school == 1) {
      return [
        "semester_id" => "required",
        "subject_id" => "required",
        "exam_rules_id" => "required",
        "major_id" => "required",
        "class_id" => "required",
        "type_exam" => "required",
        "amount_question" => "required|regex:/^[0-9]*$/",
        "name" => "required|regex:/^[a-zA-Z0-9 ]*$/|max:100",
        "start_date" => "required",
        "end_date" => "required",
        "start_time" => "required",
        "end_time" => "required",
        "duration" => "required",
        "time_violation" => "required",
        "show_value" => "required",
      ];
    } else {
      return [
        "grade_level_id" => "required",
        "subject_id" => "required",
        "exam_rules_id" => "required",
        "class_id" => "required",
        "type_exam" => "required",
        "amount_question" => "required|regex:/^[0-9]*$/",
        "name" => "required|regex:/^[a-zA-Z0-9 ]*$/|max:100",
        "start_date" => "required",
        "end_date" => "required",
        "start_time" => "required",
        "end_time" => "required",
        "duration" => "required|regex:/^[0-9]*$/",
        "time_violation" => "required|regex:/^[0-9]*$/",
        "show_value" => "required",
      ];
    }
  }

  /**
   * Get the error messages for the defined validation rules.
   *
   * @return array
   */
  public function messages()
  {
    return [
      'semester_id.required' => 'Semester tidak boleh kosong',
      'grade_level_id.required' => 'Tingkat kelas tidak boleh kosong',
      'exam_rules_id.required' => 'Jenis peraturan tidak boleh kosong',
      'major_id.required' => 'Jurusan tidak boleh kosong',
      'class_id.required' => 'Kelas tidak boleh kosong',
      'subject_id.required' => subjectName() . ' tidak boleh kosong',
      'name.required' => 'Nama ujian tidak boleh kosong',
      'name.regex' => 'Nama ujian harus huruf dan angka',
      'type_exam.required' => 'Jenis ujian tidak boleh kosong',
      'amount_question.required' => 'Jumlah soal tidak boleh kosong',
      'amount_question.regex' => 'Jumlah soal harus angka',
      'duration.regex' => 'Durasi harus angka',
      'time_violation.regex' => 'Waktu pelanggaran  harus angka',
      'start_date.required' => 'Tanggal mulai tidak boleh kosong',
      'end_date.required' => 'Tanggal akhir tidak boleh kosong',
      'start_time.required' => 'Waktu mulai tidak boleh kosong',
      'end_time.required' => 'Waktu akhir tidak boleh kosong',
      'duration.required' => 'Durasi tidak boleh kosong',
      'time_violation.required' => 'Waktu pelanggaran tidak boleh kosong',
      'show_value.required' => 'Tampil nilai tidak boleh kosong',
    ];
  }
}
