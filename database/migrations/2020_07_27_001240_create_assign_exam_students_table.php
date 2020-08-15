<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignExamStudentsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('assign_exam_students', function (Blueprint $table) {
      $table->id();
      $table->foreignId('student_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('exam_id')->constrained('manage_exams')->onUpdate('cascade')->onDelete('cascade');
      $table->integer('status')->nullable()->comment('0 = not done, 1 = done');
      $table->integer('status_generate')->nullable()->default(0)->comment('0 = not yet generated, 1 = have been generated');
      $table->integer('violation')->nullable();
      $table->string('ip_address')->nullable();
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
    Schema::dropIfExists('assign_exam_students');
  }
}
