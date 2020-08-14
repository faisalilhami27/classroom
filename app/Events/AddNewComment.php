<?php

namespace App\Events;

use App\Models\ForumComment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class AddNewComment implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;
  public $comment;
  public $postingId;

  /**
   * Create a new event instance.
   *
   * @param ForumComment $comment
   * @param $postingId
   */
  public function __construct(ForumComment $comment, $postingId)
  {
    $this->comment = $comment;
    $this->postingId = $postingId;
    $this->dontBroadcastToCurrentUser();
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return \Illuminate\Broadcasting\Channel|array
   */
  public function broadcastOn()
  {
    return new PrivateChannel('groups.' . $this->postingId);
  }

  public function broadcastWith()
  {
    if (Auth::guard('employee')->check()) {
      $user = [
        'id' => $this->comment->employee->id,
        'name' => $this->comment->employee->name . ' (Guru)',
        'photo' => (is_null($this->comment->employee->photo)) ? null : asset('storage/' . $this->comment->employee->photo)
      ];
    } else {
      $user = [
        'id' => $this->comment->student->id,
        'name' => $this->comment->student->name,
        'photo' => (is_null($this->comment->student->photo)) ? null : asset('storage/' . $this->comment->student->photo)
      ];
    }

    return [
      'message' => $this->comment->message,
      'date' => $this->comment->created_at->diffForHumans(),
      'user' => $user
    ];
  }
}
