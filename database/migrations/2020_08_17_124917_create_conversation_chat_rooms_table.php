<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversationChatRoomsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('conversation_chat_rooms', function (Blueprint $table) {
      $table->id();
      $table->text('message')->nullable();
      $table->integer('type')->nullable();
      $table->foreignId('chat_id')
        ->constrained('chat_rooms')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreignId('student_id')
        ->nullable()
        ->constrained()
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreignId('employee_id')
        ->nullable()
        ->constrained()
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreignId('receiver_employee')
        ->nullable()
        ->constrained('employees')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreignId('receiver_student')
        ->nullable()
        ->constrained('students')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->integer('status_read')->nullable();
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
    Schema::dropIfExists('conversation_chat_rooms');
  }
}
