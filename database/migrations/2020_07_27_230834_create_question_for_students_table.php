<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionForStudentsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('question_for_students', function (Blueprint $table) {
      $table->id();
      $table->foreignId('exam_question_id')
        ->constrained('accommodate_exam_questions')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreignId('assign_student_id')
        ->constrained('assign_exam_students')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreignId('remedial_exam_id')
        ->nullable()
        ->constrained()
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreignId('supplementary_exam_id')
        ->nullable()
        ->constrained()
        ->onUpdate('cascade')
        ->onDelete('cascade');
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
    Schema::dropIfExists('question_for_students');
  }
}
