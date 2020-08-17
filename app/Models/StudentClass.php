<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentClass extends Model
{
  use SoftDeletes;
  protected $table = "classes";
  protected $primaryKey = "id";
  protected $dates = ['deleted_at', 'updated_at', 'created_at'];
  protected $fillable = [
    'code',
    'class_order',
    'image',
    'color',
    'grade_level_id',
    'major_id',
    'semester_id',
    'subject_id',
    'employee_id',
    'school_year_id',
    'created_by',
    'last_updated_by',
  ];
  protected $appends = [
    'class_name'
  ];

  public function subject()
  {
    return $this->hasOne(Subject::class, 'id', 'subject_id');
  }

  public function gradeLevel()
  {
    return $this->hasOne(GradeLevel::class, 'id', 'grade_level_id');
  }

  public function major()
  {
    return $this->hasOne(Major::class, 'id', 'major_id');
  }

  public function semester()
  {
    return $this->hasOne(Semester::class, 'id', 'semester_id');
  }

  public function employee()
  {
    return $this->hasOne(Employee::class, 'id', 'employee_id');
  }

  public function schoolYear()
  {
    return $this->hasOne(SchoolYear::class, 'id', 'school_year_id');
  }

  public function getClassNameAttribute()
  {
    $config = optional(configuration())->type_school;
    $name = null;

    if ($config == 1) {
      if (is_null($this->semester)) {
        $name = "Semester belum ditentukan";
      } else {
        if (is_null($this->schoolYear)) {
          $name = "Belum ada tahun ajar aktif";
        } else {
          $name = "{$this->subject->name} Kelas {$this->class_order} ({$this->schoolYear->early_year}/{$this->schoolYear->end_year})";
        }
      }
    } else {
      if (is_null($this->gradeLevel)) {
        $name = "Tingkat kelas belum ditentukan";
      } else {
        if (is_null($this->schoolYear)) {
          $name = "Belum ada tahun ajar aktif";
        } else {
          if (optional(configuration())->type_school == 2) {
            $name = "{$this->subject->name} Kelas {$this->gradeLevel->name} - {$this->major->code} - {$this->class_order} ({$this->schoolYear->early_year}/{$this->schoolYear->end_year})";
          } else {
            $name = "{$this->subject->name} Kelas {$this->gradeLevel->name} - {$this->class_order} ({$this->schoolYear->early_year}/{$this->schoolYear->end_year})";
          }
        }
      }
    }
    return $name;
  }

  public function classTransaction()
  {
    return $this->hasMany(StudentClassTransaction::class, 'class_id', 'id');
  }
}
