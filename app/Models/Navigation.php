<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Navigation extends Model
{
  use SoftDeletes;
  protected $table = "navigations";
  protected $primaryKey = "id";
  protected $dates = ['deleted_at', 'updated_at', 'created_at'];
  protected $fillable = ["title", "icon", "url", "parent_id", "order_num", "order_sub"];

  public function userNavigation()
  {
    return $this->belongsTo(UserNavigation::class, 'id', 'navigation_id');
  }
}
