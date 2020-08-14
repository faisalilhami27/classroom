<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskFile extends Model
{
  use SoftDeletes;
  protected $table = 'task_files';
  protected $primaryKey = 'id';
  protected $fillable = ['task_id', 'file', 'filename', 'mime_t'];
}
