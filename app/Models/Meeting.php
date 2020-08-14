<?php

namespace App\Models;

use App\Traits\CustomTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meeting extends Model
{
  use SoftDeletes, CustomTrait;
  protected $table = 'meetings';
  protected $primaryKey = 'id';
  protected $fillable = ['class_id', 'url', 'meeting_id', 'password'];

  public function studentClass()
  {
    return $this->hasOne(StudentClass::class, 'id', 'class_id');
  }
}
