<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplementaryExamsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('supplementary_exams', function (Blueprint $table) {
      $table->id();
      $table->foreignId('assign_student_id')
        ->constrained('assign_exam_students')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->dateTime('start_date');
      $table->dateTime('end_date');
      $table->integer('status')->nullable()->comment('0 = not done, 1 = done');
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
    Schema::dropIfExists('supplementary_exams');
  }
}
