<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
  use SoftDeletes;
  protected $table = "materials";
  protected $primaryKey = "id";
  protected $dates = ['deleted_at', 'updated_at', 'created_at'];
  protected $fillable = [
    'employee_id',
    'semester_id',
    'grade_level_id',
    'subject_id',
    'major_id',
    'position',
    'title',
    'video_link',
    'content',
    'module',
    'archive',
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
}
