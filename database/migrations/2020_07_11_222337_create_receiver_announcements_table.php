<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiverAnnouncementsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('receiver_announcements', function (Blueprint $table) {
      $table->id();
      $table->foreignId('announcement_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('employee_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('student_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->integer('status_read')->comment('1 = unread, 2 = read');
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
    Schema::dropIfExists('receiver_announcements');
  }
}
