<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionBank extends Model
{
  use SoftDeletes;
  protected $table = "question_banks";
  protected $primaryKey = "id";
  protected $dates = ['deleted_at', 'updated_at', 'created_at'];
  protected $fillable = [
    "question_name",
    "subject_id",
    "semester_id",
    "grade_level_id",
    "major_id",
    "school_year_id",
    "employee_id",
    "extension",
    "document",
    "type_question",
    'created_by',
    'last_updated_by',
  ];

  public function subject()
  {
    return $this->hasOne(Subject::class, 'id', 'subject_id');
  }

  public function semester()
  {
    return $this->hasOne(Semester::class, 'id', 'semester_id');
  }

  public function gradeLevel()
  {
    return $this->hasOne(GradeLevel::class, 'id', 'grade_level_id');
  }

  public function employee()
  {
    return $this->hasOne(Employee::class, 'id', 'employee_id');
  }

  public function correctAnswer()
  {
    return $this->belongsTo(AnswerKey::class, 'id', 'question_id')
      ->where('key', 1);
  }

  public function answerKey()
  {
    return $this->hasMany(AnswerKey::class, 'question_id', 'id');
  }

  public function studentAnswer()
  {
    return $this->belongsTo(AccommodateExamStudentAnswer::class, 'id', 'question_id');
  }

  public function accommodateQuestion()
  {
    return $this->belongsTo(AccommodateExamQuestion::class, 'id', 'question_bank_id');
  }
}
