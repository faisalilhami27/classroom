<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamClassTransaction extends Model
{
  protected $table = 'exam_class_transactions';
  protected $primaryKey = 'id';
  protected $fillable = ['exam_id', 'class_id'];

  public function studentClass()
  {
    return $this->hasOne(StudentClass::class, 'id', 'class_id');
  }
}
