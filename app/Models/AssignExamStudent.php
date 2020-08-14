<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignExamStudent extends Model
{
  use SoftDeletes;
  protected $table = 'assign_exam_students';
  protected $primaryKey = 'id';
  protected $fillable = [
    'exam_id',
    'student_id',
    'status',
    'violation',
    'status_generate',
    'ip_address'
  ];

  public function student()
  {
    return $this->hasOne(Student::class, 'id', 'student_id');
  }

  public function exam()
  {
    return $this->hasOne(ManageExam::class, 'id', 'exam_id');
  }

  public function questionExamStudent()
  {
    return $this->hasMany(QuestionForStudent::class, 'assign_student_id', 'id');
  }

  public function scoreStudent()
  {
    return $this->hasOne(StudentExamScore::class, 'assign_student_id', 'id');
  }

  public function scoreStudents()
  {
    return $this->hasMany(StudentExamScore::class, 'assign_student_id', 'id');
  }

  public function violationStudent()
  {
    return $this->belongsTo(StudentExamViolation::class, 'id', 'assign_student_id');
  }
}
