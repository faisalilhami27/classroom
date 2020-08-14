<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolYearsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('school_years', function (Blueprint $table) {
      $table->id();
      $table->year('early_year');
      $table->year('end_year');
      $table->integer('semester');
      $table->integer('status_active')->nullable();
      $table->integer('status_passed')->nullable();
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
    Schema::dropIfExists('school_years');
  }
}
