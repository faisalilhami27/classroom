<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RemedialExam extends Model
{
  use SoftDeletes;
  protected $table = 'remedial_exams';
  protected $fillable = ['assign_student_id', 'start_date', 'end_date', 'exam_to', 'status'];

  public function assignStudent()
  {
    return $this->hasOne(AssignExamStudent::class, 'id', 'assign_student_id');
  }

  public function answer()
  {
    return $this->belongsTo(AccommodateExamStudentAnswer::class, 'id', 'remedial_id');
  }

  public function scoreStudent()
  {
    return $this->belongsTo(StudentExamScore::class, 'id', 'remedial_id');
  }
}
