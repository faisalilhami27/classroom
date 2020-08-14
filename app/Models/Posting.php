<?php

namespace App\Models;

use App\Traits\CustomTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Posting extends Model
{
  use SoftDeletes, CustomTrait;

  protected $table = "postings";
  protected $primaryKey = "id";
  protected $dates = ['deleted_at', 'updated_at', 'created_at'];
  protected $fillable = ['title', 'date', 'type_post', 'class_id', 'employee_id', 'student_id'];

  public function getImages()
  {
    return $this->hasMany(PostingImage::class, 'posting_id', 'id');
  }

  public function student()
  {
    return $this->hasOne(Student::class, 'id', 'student_id');
  }

  public function employee()
  {
    return $this->hasOne(Employee::class, 'id', 'employee_id');
  }

  public function task()
  {
    return $this->belongsTo(Task::class, 'id', 'posting_id');
  }

  public function forum()
  {
    return $this->belongsTo(ForumComment::class, 'id', 'posting_id');
  }
}
