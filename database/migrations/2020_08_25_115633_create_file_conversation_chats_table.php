<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileConversationChatsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('file_conversation_chats', function (Blueprint $table) {
      $table->id();
      $table->foreignId('conversation_id')
        ->constrained('conversation_chat_rooms')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->string('file', 255);
      $table->string('filename', 255);
      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('file_conversation_chats');
  }
}
