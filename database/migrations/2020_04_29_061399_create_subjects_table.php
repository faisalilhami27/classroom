<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubjectsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('subjects', function (Blueprint $table) {
      $table->id();
      $table->string('code', 10);
      $table->string('name', 100);
      $table->bigInteger('semester_id')->nullable()->unsigned()->comment('only filled the type school university');
      $table->bigInteger('major_id')->nullable()->unsigned();
      $table->timestamps();
      $table->softDeletes();

      $table->foreign('semester_id')->references('id')->on('semesters')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->foreign('major_id')->references('id')->on('majors')
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
    Schema::dropIfExists('subjects');
  }
}
