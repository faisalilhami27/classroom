<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccommodateExamStudentAnswer extends Model
{
  use SoftDeletes;
  protected $table = 'accommodate_exam_student_answers';
  protected $fillable = ['student_id', 'exam_id', 'question_id', 'answer_id', 'remedial_id', 'supplementary_id', 'hesitate'];
}
