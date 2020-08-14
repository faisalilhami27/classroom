<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class UserStudent extends Authenticatable
{
  use Notifiable;
  use SoftDeletes;
  protected $table = 'user_students';
  protected $guard = 'student';
  protected $fillable = ['username', 'password', 'status', 'email_verified', 'student_id'];
  protected $hidden = ['password'];

  public function student()
  {
    return $this->hasOne(Student::class, 'id', 'student_id');
  }
}
