<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumComment extends Model
{
  protected $table = 'forum_comments';
  protected $fillable = ['user_id', 'user_type', 'message', 'posting_id'];

  public function commentable()
  {
    return $this->morphTo();
  }

  public function employee()
  {
    return $this->belongsTo(Employee::class, 'user_id', 'id');
  }

  public function student()
  {
    return $this->belongsTo(Student::class, 'user_id', 'id');
  }

  public function posting()
  {
    return $this->belongsTo(Posting::class, 'id', 'posting_id');
  }
}
