<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnswerDiscussionEvent implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;
  public $answer;
  public $count;
  public $classId;
  public $discussionId;
  public $type;

  /**
   * Create a new event instance.
   *
   * @param $answer
   * @param $classId
   * @param $count
   * @param $discussionId
   * @param $type
   */
  public function __construct($answer, $classId, $count, $discussionId, $type)
  {
    $this->answer = $answer;
    $this->classId = $classId;
    $this->count = $count;
    $this->discussionId = $discussionId;
    $this->type = $type;
    $this->dontBroadcastToCurrentUser();
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return \Illuminate\Broadcasting\Channel|array
   */
  public function broadcastOn()
  {
    return new PresenceChannel('class-answer.' . $this->classId . '.' . $this->discussionId);
  }
}
