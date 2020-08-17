<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationChatRoom extends Model
{
  protected $table = 'conversation_chat_rooms';
  protected $fillable = ['student_id', 'employee_id', 'chat_id', 'message'];

  public function chat()
  {
    return $this->hasOne(ChatRoom::class, 'id', 'chat_id');
  }
}
