<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
  use SoftDeletes;

  protected $table = "employees";
  protected $primaryKey = "id";
  protected $dates = ['deleted_at', 'updated_at', 'created_at'];
  protected $fillable = ["name", "first_name", "last_name", "email", "phone_number", "employee_identity_number", "photo", "color"];

  public function userEmployee()
  {
    return $this->belongsTo(UserEmployee::class, 'id', 'employee_id');
  }

  public function comment()
  {
    return $this->morphMany(ForumComment::class, 'commentable');
  }
}
