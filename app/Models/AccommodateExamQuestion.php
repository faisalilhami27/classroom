<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccommodateExamQuestion extends Model
{
  use SoftDeletes;
  protected $table = "accommodate_exam_questions";
  protected $primaryKey = "id";
  protected $dates = ['deleted_at', 'updated_at', 'created_at'];
  protected $fillable = ['exam_id', 'question_bank_id'];

  public function exam()
  {
    return $this->hasOne(ManageExam::class, 'id', 'exam_id');
  }

  public function questionBank()
  {
    return $this->hasOne(QuestionBank::class, 'id', 'question_bank_id');
  }
}
