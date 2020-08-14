<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MinimalCompletenessCriteria extends Model
{
  use SoftDeletes;
  protected $table = "minimal_completeness_criterias";
  protected $primaryKey = "id";
  protected $dates = ['deleted_at', 'updated_at', 'created_at'];
  protected $fillable = ["subject_id", "school_year_id", "grade_level_id", "semester_id", "minimal_criteria", 'created_by', 'last_updated_by',];

  public function subject()
  {
    return $this->hasOne(Subject::class, 'id', 'subject_id');
  }

  public function gradeLevel()
  {
    return $this->hasOne(GradeLevel::class, 'id', 'grade_level_id');
  }

  public function semester()
  {
    return $this->hasOne(Semester::class, 'id', 'semester_id');
  }
}
