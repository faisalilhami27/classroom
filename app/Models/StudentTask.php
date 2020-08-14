<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentTask extends Model
{
  use SoftDeletes;
  protected $table = 'student_tasks';
  protected $primaryKey = 'id';
  protected $fillable = ['task_id', 'student_id', 'task_file', 'status', 'filename', 'mime_type', 'score'];

  public function student()
  {
    return $this->hasOne(Student::class, 'id', 'student_id');
  }

  public function task()
  {
    return $this->hasOne(Task::class, 'id', 'task_id');
  }
}
