<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostingImage extends Model
{
  use SoftDeletes;
  protected $table = "posting_images";
  protected $primaryKey = "id";
  protected $dates = ['deleted_at', 'updated_at', 'created_at'];
  protected $fillable = ['posting_id', 'file', 'filename', 'mime_type'];
}
