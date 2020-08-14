<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class UserEmployee extends Authenticatable
{
  use Notifiable;
  use SoftDeletes;
  protected $table = 'user_employees';
  protected $guard = 'employee';
  protected $fillable = ['employee_id', 'username', 'password', 'status', 'status_generate', 'user_id_zoom'];
  protected $hidden = ['password'];

  public function employee()
  {
    return $this->hasOne(Employee::class, 'id', 'employee_id');
  }

  public function roles()
  {
    return $this->belongsToMany(Role::class, RoleUser::class, 'user_employee_id', 'role_id')->withTimestamps();
  }

  public function hasAnyRole($roles)
  {
    $array = explode('|', $roles);
    if (is_array($array)) {
      foreach ($array as $role) {
        if ($this->hasRole($role)) {
          return true;
        }
      }
    } else {
      if ($this->hasRole($array)) {
        return true;
      }
    }
    return false;
  }

  public function hasRole($role)
  {
    $checkRole = $this->roles()->where('name', $role)->first();
    if ($checkRole) {
      return true;
    }
    return false;
  }
}
