<?php

namespace App\Models;

use App\Traits\CustomTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceiverAnnouncement extends Model
{
  use SoftDeletes, CustomTrait;
  protected $table = 'receiver_announcements';
  protected $primaryKey = 'id';
  protected $fillable = ['announcement_id', 'employee_id', 'student_id', 'status_read'];

  public function announcement()
  {
    return $this->belongsTo(Announcement::class, 'id', 'announcement_id');
  }
}
