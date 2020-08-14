<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
  protected $table = "configurations";
  protected $primaryKey = "id";
  protected $dates = ['updated_at', 'created_at'];
  protected $fillable = ["school_name", "school_logo", "type_school", "reset_password_employee" , "reset_password_student"];
}
