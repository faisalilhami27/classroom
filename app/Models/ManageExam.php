<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageExam extends Model
{
  use SoftDeletes;
  protected $table = "manage_exams";
  protected $primaryKey = "id";
  protected $dates = ['deleted_at', 'updated_at', 'created_at'];
  protected $fillable = [
    'employee_id',
    'semester_id',
    'grade_level_id',
    'subject_id',
    'exam_rules_id',
    'major_id',
    'type_exam',
    'name',
    'start_date',
    'end_date',
    'duration',
    'amount_question',
    'select_question',
    'status',
    'time_violation',
    'show_value',
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

  public function examRules()
  {
    return $this->hasOne(ExamRules::class, 'id', 'exam_rules_id');
  }

  public function examClass()
  {
    return $this->hasOne(ExamClassTransaction::class, 'exam_id', 'id');
  }

  public function assignStudent()
  {
    return $this->hasMany(AssignExamStudent::class, 'exam_id', 'id');
  }

  public function singleAssignStudent()
  {
    return $this->hasOne(AssignExamStudent::class, 'exam_id', 'id');
  }

  public function question()
  {
    return $this->hasMany(AccommodateExamQuestion::class, 'exam_id', 'id');
  }
}
