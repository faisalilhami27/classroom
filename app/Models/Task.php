<?php

namespace App\Models;

use App\Traits\CustomTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
  use SoftDeletes, CustomTrait;

  protected $table = 'tasks';
  protected $primaryKey = 'id';
  protected $fillable = [
    'title',
    'date',
    'deadline_date',
    'time',
    'description',
    'topic',
    'max_score',
    'task_to',
    'show_score',
    'employee_id',
    'class_id',
    'posting_id',
  ];

  public function taskFiles()
  {
    return $this->hasMany(TaskFile::class, 'task_id', 'id');
  }

  public function studentTasks()
  {
    return $this->hasMany(StudentTask::class, 'task_id', 'id');
  }

  public function posting()
  {
    return $this->hasOne(Posting::class, 'id', 'posting_id');
  }

  public function studentClass()
  {
    return $this->hasOne(StudentClass::class, 'id', 'class_id');
  }
}
