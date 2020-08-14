<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionBanksTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('question_banks', function (Blueprint $table) {
      $table->id();
      $table->text('question_name')->nullable();
      $table->bigInteger('subject_id')->unsigned();
      $table->bigInteger('semester_id')->unsigned()->nullable();
      $table->bigInteger('school_year_id')->unsigned()->nullable();
      $table->bigInteger('grade_level_id')->unsigned()->nullable();
      $table->bigInteger('employee_id')->unsigned();
      $table->integer('type_question');
      $table->string('extension', 20)->nullable();
      $table->text('document')->nullable();
      $table->foreignId('created_by')->nullable()->constrained('employees')->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('last_updated_by')->nullable()->constrained('employees')->onUpdate('cascade')->onDelete('cascade');
      $table->timestamps();
      $table->softDeletes();

      $table->foreign('subject_id')->references('id')->on('subjects')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreign('semester_id')->references('id')->on('semesters')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreign('school_year_id')->references('id')->on('school_years')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreign('grade_level_id')->references('id')->on('grade_levels')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreign('employee_id')->references('id')->on('employees')
        ->onUpdate('cascade')
        ->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('question_banks');
  }
}
