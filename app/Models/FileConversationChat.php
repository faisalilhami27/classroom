<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileConversationChat extends Model
{
  use SoftDeletes;
  protected $table = 'file_conversation_chats';
  protected $fillable = ['conversation_id', 'file', 'filename'];

  public function conversation()
  {
   return $this->hasOne(ConversationChatRoom::class, 'id', 'conversation_id');
  }
}
