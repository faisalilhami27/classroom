<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserNavigation extends Model
{
  use SoftDeletes;
  protected $table = "user_navigations";
  protected $primaryKey = "id";
  protected $dates = ['deleted_at', 'updated_at', 'created_at'];
  protected $fillable = ["role_id", "navigation_id", "create", "read", "update", "delete"];

  public function navigation()
  {
    return $this->hasOne(Navigation::class, 'id', 'navigation_id');
  }

  public function role()
  {
    return $this->hasOne(Role::class, 'id', 'role_id');
  }
}
