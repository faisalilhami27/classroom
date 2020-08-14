<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccommodateExamStudentAnswersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('accommodate_exam_student_answers', function (Blueprint $table) {
      $table->id();
      $table->foreignId('exam_id')->nullable()->constrained('manage_exams')->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('student_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('question_id')->nullable()->constrained('question_banks')->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('answer_id')->nullable()->constrained('answer_keys')->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('remedial_id')->nullable()->constrained('remedial_exams')->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('supplementary_id')->nullable()->constrained('supplementary_exams')->onUpdate('cascade')->onDelete('cascade');
      $table->integer('hesitate')->nullable();
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
    Schema::dropIfExists('accommodate_exam_student_answers');
  }
}
