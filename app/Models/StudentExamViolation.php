<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentExamViolation extends Model
{
  protected $table = 'student_exam_violations';
  protected $fillable = ['assign_student_id', 'remedial_id', 'supplementary_ud', 'violation_name'];
}
