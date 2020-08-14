<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
  use SoftDeletes;
  protected $table = "subjects";
  protected $primaryKey = "id";
  protected $dates = ['deleted_at', 'updated_at', 'created_at'];
  protected $fillable = ['code', 'name', 'semester_id', 'major_id'];

  public function minimalCriteria()
  {
    return $this->belongsTo(MinimalCompletenessCriteria::class, 'subject_id', 'id');
  }

  public function major()
  {
    return $this->hasOne(Major::class, 'id', 'major_id');
  }

  public function semester()
  {
    return $this->hasOne(Semester::class, 'id', 'semester_id');
  }
}
