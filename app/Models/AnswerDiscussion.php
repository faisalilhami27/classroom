<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnswerDiscussion extends Model
{
  use SoftDeletes;
  protected $table = 'answer_discussions';
  protected $fillable = ['message', 'discussion_id', 'student_id', 'employee_id'];
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

  public function student()
  {
    return $this->hasOne(Student::class, 'id', 'student_id');
  }

  public function employee()
  {
    return $this->hasOne(Employee::class, 'id', 'employee_id');
  }

  public function discussion()
  {
    return $this->belongsTo(Discussion::class, 'discussion_id', 'id');
  }
}
