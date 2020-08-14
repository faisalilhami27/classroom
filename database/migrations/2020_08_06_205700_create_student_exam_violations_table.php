<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentExamViolationsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('student_exam_violations', function (Blueprint $table) {
      $table->id();
      $table->foreignId('assign_student_id')->constrained('assign_exam_students')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreignId('remedial_id')
        ->nullable()
        ->constrained('remedial_exams')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreignId('supplementary_id')
        ->nullable()
        ->constrained('supplementary_exams')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->string('violation_name', 255);
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
    Schema::dropIfExists('student_exam_violations');
  }
}
