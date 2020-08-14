<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
  use SoftDeletes;
  protected $table = 'announcements';
  protected $primaryKey = 'id';
  protected $fillable = ['title', 'content', 'type', 'created_by_employee', 'end_date', 'created_by_student', 'class_id'];

  public function receiverAnnouncement()
  {
    return $this->hasMany(ReceiverAnnouncement::class, 'announcement_id', 'id');
  }

  public function meeting()
  {
    return $this->hasOne(Meeting::class, 'id', 'meeting_id');
  }

  public function posting()
  {
    return $this->hasOne(Posting::class, 'id', 'posting_id');
  }

  public function employee()
  {
    return $this->hasOne(Employee::class, 'id', 'created_by_employee');
  }

  public function student()
  {
    return $this->hasOne(Student::class, 'id', 'created_by_student');
  }

  public function studentClass()
  {
   return $this->hasOne(StudentClass::class, 'id', 'class_id');
  }
}
