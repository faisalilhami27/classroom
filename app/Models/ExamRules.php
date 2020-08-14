<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamRules extends Model
{
  use SoftDeletes;
  protected $table = "exam_rules";
  protected $primaryKey = "id";
  protected $dates = ['deleted_at', 'updated_at', 'created_at'];
  protected $fillable = ['text', 'name', 'employee_id', 'created_by', 'last_updated_by',];
}
