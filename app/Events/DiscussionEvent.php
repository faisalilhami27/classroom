<?php

namespace App\Events;

use App\Models\Discussion;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DiscussionEvent implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $discussion;
  public $discussionId;
  public $classId;
  public $count;
  public $type;

  /**
   * Create a new event instance.
   *
   * @param $discussion
   * @param $classId
   * @param $count
   * @param $type
   * @param null $discussionId
   */
  public function __construct($discussion = null, $classId = null, $count = null, $type = null, $discussionId = null)
  {
    $this->discussion = $discussion;
    $this->classId = $classId;
    $this->count = $count;
    $this->type = $type;
    $this->discussionId = $discussionId;
    $this->dontBroadcastToCurrentUser();
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return \Illuminate\Broadcasting\Channel|array
   */
  public function broadcastOn()
  {
    return new PresenceChannel('class.' . $this->classId);
  }
}
