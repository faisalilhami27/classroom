<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
  use SoftDeletes;
  protected $table = "students";
  protected $primaryKey = "id";
  protected $dates = ['deleted_at', 'updated_at', 'created_at'];
  protected $fillable = ["name", "email", "phone_number", "student_identity_number", "photo"];

  public function userStudent()
  {
    return $this->belongsTo(UserStudent::class, 'id', 'student_id');
  }

  public function comment()
  {
    return $this->morphMany(ForumComment::class, 'commentable');
  }

  public function studentClassTransaction()
  {
    return $this->belongsTo(StudentClassTransaction::class, 'id', 'student_id');
  }

  public function examStudent()
  {
    return $this->belongsTo(AssignExamStudent::class, 'id', 'student_id');
  }
}
