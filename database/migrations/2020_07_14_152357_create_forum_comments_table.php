<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForumCommentsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('forum_comments', function (Blueprint $table) {
      $table->id();
      $table->text('message');
      $table->foreignId('posting_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->integer('user_id');
      $table->string('user_type', 20);
      $table->integer('commentable_id');
      $table->string('commentable_type', 50);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('forum_comments');
  }
}
