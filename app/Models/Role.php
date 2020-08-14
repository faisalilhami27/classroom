<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
  use SoftDeletes;

  protected $table = "roles";
  protected $primaryKey = "id";
  protected $dates = ['deleted_at', 'updated_at', 'created_at'];
  protected $fillable = ['name'];

  public function users()
  {
    $this->belongsToMany(UserEmployee::class, RoleUser::class, 'role_id', 'user_employee_id');
  }
}
