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

  /**
   * Create a new event instance.
   *
   * @param $chat
   * @param $userId
   */
  public function __construct(ConversationChatRoom $chat, $userId)
  {
    $this->userId = $userId;
    $this->chat = $chat;
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
