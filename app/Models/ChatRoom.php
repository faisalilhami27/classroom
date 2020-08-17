<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
  protected $table = 'chat_rooms';
  protected $fillable = ['student_id', 'employee_id'];

  public function employee()
  {
    return $this->hasOne(Employee::class, 'id', 'employee_id');
  }

  public function student()
  {
    return $this->hasOne(Student::class, 'id', 'student_id');
  }

  public function conversation()
  {
    return $this->hasMany(ConversationChatRoom::class, 'chat_id', 'id');
  }
}
