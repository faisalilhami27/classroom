<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscussionsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('discussions', function (Blueprint $table) {
      $table->id();
      $table->text('message');
      $table->foreignId('material_id')
        ->nullable()
        ->constrained()
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreignId('class_id')
        ->nullable()
        ->constrained()
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreignId('student_id')
        ->nullable()
        ->constrained()
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreignId('employee_id')
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
    Schema::dropIfExists('discussions');
  }
}
