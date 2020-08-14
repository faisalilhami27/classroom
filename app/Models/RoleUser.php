<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
    protected $table = "role_users";
    protected $primaryKey = "id";
    protected $dates = ['deleted_at', 'updated_at', 'created_at'];
    protected $fillable = ['user_employee_id', 'role_id'];

  public function role()
  {
    return $this->belongsTo(Role::class, 'role_id', 'id');
  }

  public function userEmployee()
  {
    return $this->belongsTo(UserEmployee::class, 'user_employee_id', 'id');
  }
}
