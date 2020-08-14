<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('meetings', function (Blueprint $table) {
      $table->id();
      $table->foreignId('class_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->string('url', 255)->nullable();
      $table->string('meeting_id', 20)->nullable();
      $table->char('password', 6)->nullable();
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
    Schema::dropIfExists('meetings');
  }
}
