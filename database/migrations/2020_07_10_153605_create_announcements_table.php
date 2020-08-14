<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('announcements', function (Blueprint $table) {
      $table->id();
      $table->string('title', 255);
      $table->foreignId('class_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->text('content')->nullable();
      $table->integer('type')->nullable()->comment('1 = meeting, 2 = posting');
      $table->integer('created_by_employee')->nullable()->constrained('employees')->onUpdate('cascade')->onDelete('cascade');
      $table->integer('created_by_student')->nullable()->constrained('students')->onUpdate('cascade')->onDelete('cascade');
      $table->date('end_date');
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
    Schema::dropIfExists('announcements');
  }
}
