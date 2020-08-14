<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnswerKey extends Model
{
  use SoftDeletes;
  protected $table = "answer_keys";
  protected $primaryKey = "id";
  protected $dates = ['deleted_at', 'updated_at', 'created_at'];
  protected $fillable = ["answer_name", "key", "question_id", "employee_id", 'created_by', 'last_updated_by',];

  public function question()
  {
    return $this->hasOne(QuestionBank::class, 'id', 'question_id');
  }
}
