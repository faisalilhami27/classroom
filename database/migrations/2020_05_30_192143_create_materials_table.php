<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('materials', function (Blueprint $table) {
      $table->id();
      $table->bigInteger('school_year_id')->nullable()->unsigned();
      $table->bigInteger('employee_id')->nullable()->unsigned();
      $table->bigInteger('major_id')->nullable()->unsigned();
      $table->bigInteger('semester_id')->nullable()->unsigned()->comment('filled when the type of school is a university');
      $table->bigInteger('grade_level_id')->nullable()->unsigned()->comment('filled when the type of school is a school');
      $table->bigInteger('subject_id')->nullable()->unsigned();
      $table->integer('position');
      $table->string('title', 100)->nullable();
      $table->text('video_link')->nullable();
      $table->longText('content')->nullable();
      $table->string('module')->nullable();
      $table->string('archive')->nullable();
      $table->foreignId('created_by')->nullable()->constrained('employees')->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('last_updated_by')->nullable()->constrained('employees')->onUpdate('cascade')->onDelete('cascade');
      $table->timestamps();
      $table->softDeletes();

      $table->foreign('employee_id')->references('id')->on('employees')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreign('major_id')->references('id')->on('employees')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreign('school_year_id')->references('id')->on('school_years')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreign('semester_id')->references('id')->on('semesters')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreign('grade_level_id')->references('id')->on('grade_levels')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreign('subject_id')->references('id')->on('subjects')
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
    Schema::dropIfExists('materials');
  }
}
