<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccommodateExamQuestionsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('accommodate_exam_questions', function (Blueprint $table) {
      $table->id();
      $table->foreignId('exam_id')->nullable()->constrained('manage_exams')->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('question_bank_id')->nullable()->constrained('question_banks')->onUpdate('cascade')->onDelete('cascade');
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
    Schema::dropIfExists('accommodate_exam_questions');
  }
}
