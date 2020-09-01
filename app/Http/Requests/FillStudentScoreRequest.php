<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class FillStudentScoreRequest extends FormRequest
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
    $taskId = request('task_id');
    $task = Task::where('id', $taskId)->first();
    return [
      'score' => 'required|regex:/^[0-9]*$/|lte:' . $task->max_score
    ];
  }

  /**
   * Get the error messages for the defined validation rules.
   *
   * @return array
   */
  public function messages()
  {
    $taskId = request('task_id');
    $task = Task::where('id', $taskId)->first();
    return [
      'score.required' => 'Nilai tidak boleh kosong',
      'score.regex' => 'Nilai harus berupa angka',
      'score.lte' => 'Nilai tidak boleh lebih dari ' . $task->max_score,
    ];
  }
}
