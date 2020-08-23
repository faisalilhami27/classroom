<?php

use App\Models\ForumComment;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('groups.{group}', function ($user) {
  return $user;
}, ['guards' => ['employee', 'student']]);

Broadcast::channel('user.{id}', function ($user) {
  return $user;
}, ['guards' => ['employee', 'student']]);

Broadcast::channel('chatroom', function ($user) {
  return $user;
}, ['guards' => ['employee', 'student']]);

Broadcast::channel('class.{id}', function ($user) {
  return $user;
}, ['guards' => ['employee', 'student']]);

Broadcast::channel('class-answer.{id}.{id2}', function ($user) {
  return $user;
}, ['guards' => ['employee', 'student']]);

Broadcast::channel('delete-discussion.{id}', function ($user) {
  return $user;
}, ['guards' => ['employee', 'student']]);

Broadcast::channel('delete-answer.{id}.{id2}', function ($user) {
  return $user;
}, ['guards' => ['employee', 'student']]);
