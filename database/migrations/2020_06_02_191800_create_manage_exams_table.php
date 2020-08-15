<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManageExamsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('manage_exams', function (Blueprint $table) {
      $table->id();
      $table->integer('type_exam');
      $table->string('name', 100);
      $table->dateTime('start_date');
      $table->dateTime('end_date');
      $table->integer('duration');
      $table->integer('amount_question');
      $table->integer('select_question')->nullable();
      $table->integer('status')->nullable();
      $table->integer('time_violation')->nullable();
      $table->integer('show_value')->nullable();
      $table->foreignId('major_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('semester_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('grade_level_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('exam_rules_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('subject_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('employee_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('created_by')->nullable()->constrained('employees')->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('last_updated_by')->nullable()->constrained('employees')->onUpdate('cascade')->onDelete('cascade');
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
    Schema::dropIfExists('manage_exams');
  }
}
