<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('tasks', function (Blueprint $table) {
      $table->id();
      $table->text('title');
      $table->date('date');
      $table->date('deadline_date');
      $table->time('time');
      $table->text('description')->nullable();
      $table->text('topic')->nullable();
      $table->integer('max_score');
      $table->integer('task_to');
      $table->integer('show_score')->nullable()->comment('1 = not shown, 2 = shown')->default(1);
      $table->foreignId('employee_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('class_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('posting_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
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
    Schema::dropIfExists('tasks');
  }
}
