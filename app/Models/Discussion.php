<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discussion extends Model
{
  use SoftDeletes;

  protected $table = 'discussions';
  protected $fillable = ['message', 'student_id', 'employee_id', 'material_id', 'class_id'];
  protected $appends = [
    'post_time',
    'update_post_time',
  ];

  public function getPostTimeAttribute()
  {
    return $this->created_at->diffForHumans();
  }

  public function getUpdatePostTimeAttribute()
  {
    return $this->updated_at->diffForHumans();
  }

  public function answer()
  {
    return $this->hasMany(AnswerDiscussion::class, 'discussion_id', 'id');
  }

  public function student()
  {
    return $this->hasOne(Student::class, 'id', 'student_id');
  }

  public function employee()
  {
    return $this->hasOne(Employee::class, 'id', 'employee_id');
  }
}
