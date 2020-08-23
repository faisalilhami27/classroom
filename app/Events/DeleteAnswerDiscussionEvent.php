<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeleteAnswerDiscussionEvent implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;
  public $classId;
  public $discussionId;
  public $answerId;

  /**
   * Create a new event instance.
   *
   * @param $classId
   * @param $discussionId
   * @param $answerId
   */
  public function __construct($classId, $discussionId, $answerId)
  {
    $this->classId = $classId;
    $this->discussionId = $discussionId;
    $this->answerId = $answerId;
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return \Illuminate\Broadcasting\Channel|array
   */
  public function broadcastOn()
  {
    return new PresenceChannel('delete-answer.' . $this->classId . '.' . $this->discussionId);
  }
}
