<?php

namespace App\Models;

use App\Traits\CustomTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConversationChatRoom extends Model
{
  use SoftDeletes;
  use CustomTrait;
  protected $table = 'conversation_chat_rooms';
  protected $fillable = [
    'student_id',
    'employee_id',
    'chat_id',
    'message',
    'status_read',
    'receiver_employee',
    'receiver_student',
  ];

  public function chat()
  {
    return $this->hasOne(ChatRoom::class, 'id', 'chat_id');
  }

  public function student()
  {
    return $this->hasOne(Student::class, 'id', 'student_id');
  }

  public function employee()
  {
    return $this->hasOne(Employee::class, 'id', 'employee_id');
  }
}
