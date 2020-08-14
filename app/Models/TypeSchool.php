<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeSchool extends Model
{
  protected $table = "type_schools";
  protected $primaryKey = "id";
  protected $dates = ['deleted_at', 'updated_at', 'created_at'];
  protected $fillable = ["name"];
}
