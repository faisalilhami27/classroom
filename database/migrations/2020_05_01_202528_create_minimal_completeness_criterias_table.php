<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMinimalCompletenessCriteriasTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('minimal_completeness_criterias', function (Blueprint $table) {
      $table->id();
      $table->integer('minimal_criteria');
      $table->bigInteger('subject_id')->unsigned()->nullable();
      $table->bigInteger('grade_level_id')->unsigned()->nullable();
      $table->bigInteger('semester_id')->unsigned()->nullable();
      $table->bigInteger('school_year_id')->unsigned()->nullable();
      $table->foreignId('created_by')->nullable()->constrained('employees')->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('last_updated_by')->nullable()->constrained('employees')->onUpdate('cascade')->onDelete('cascade');
      $table->timestamps();
      $table->softDeletes();

      $table->foreign('subject_id')->references('id')->on('subjects')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreign('grade_level_id')->references('id')->on('grade_levels')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreign('school_year_id')->references('id')->on('school_years')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreign('semester_id')->references('id')->on('semesters')
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
    Schema::dropIfExists('minimal_completeness_criterias');
  }
}
