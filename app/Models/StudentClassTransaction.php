<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentClassTransaction extends Model
{
  use SoftDeletes;
  protected $table = "student_class_transactions";
  protected $primaryKey = "id";
  protected $dates = ['deleted_at', 'updated_at', 'created_at'];
  protected $fillable = ['student_id', 'class_id'];

  public function student()
  {
    return $this->hasOne(Student::class, 'id', 'student_id');
  }

  public function studentClass()
  {
    return $this->hasOne(StudentClass::class, 'id', 'class_id');
  }
}
