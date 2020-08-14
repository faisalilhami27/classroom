<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentTasksTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('student_tasks', function (Blueprint $table) {
      $table->id();
      $table->foreignId('task_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('student_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->string('task_file', 255)->nullable();
      $table->string('filename', 255)->nullable();
      $table->string('mime_type', 255)->nullable();
      $table->double('score')->nullable()->default(0);
      $table->integer('status')->nullable()->comment('1 = late, 2 = collect, 3 = not collect');
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
    Schema::dropIfExists('student_tasks');
  }
}
