<?php

namespace App\Events;

use App\Models\ConversationChatRoom;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChattingMessage implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $userId;
  public $chat;
  public $type;
  public $conversation;

  /**
   * Create a new event instance.
   *
   * @param $chat
   * @param $userId
   * @param null $type
   * @param $conversation
   */
  public function __construct($chat, $userId, $conversation = null, $type = null)
  {
    $this->userId = $userId;
    $this->type = $type;
    $this->chat = $chat;
    $this->conversation = $conversation;
    $this->dontBroadcastToCurrentUser();
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return \Illuminate\Broadcasting\Channel|array
   */
  public function broadcastOn()
  {
    return new PresenceChannel('user.' . $this->userId);
  }
}
