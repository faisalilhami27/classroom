<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionForStudent extends Model
{
  use SoftDeletes;
  protected $table = 'question_for_students';
  protected $fillable = ['exam_question_id', 'assign_student_id', 'remedial_exam_id', 'supplementary_exam_id'];

  public function accommodateExamQuestion()
  {
    return $this->hasOne(AccommodateExamQuestion::class, 'id', 'exam_question_id');
  }

  public function assignStudent()
  {
    return $this->hasOne(AssignExamStudent::class, 'id', 'assign_student_id');
  }
}
