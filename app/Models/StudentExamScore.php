<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentExamScore extends Model
{
  protected $table = 'student_exam_scores';
  protected $fillable = ['assign_student_id', 'score', 'violation', 'remedial_id', 'supplementary_id'];

  public function assignStudent()
  {
    return $this->hasOne(AssignExamStudent::class, 'id', 'assign_student_id');
  }

  public function remedial()
  {
    return $this->hasOne(RemedialExam::class,'id', 'remedial_id');
  }

  public function supplementary()
  {
    return $this->hasOne(RemedialExam::class,'id', 'supplementary_id');
  }
}
