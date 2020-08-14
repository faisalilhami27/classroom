<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigurationsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('configurations', function (Blueprint $table) {
      $table->id();
      $table->string('school_name', 100)->nullable();
      $table->string('school_logo', 100)->nullable();
      $table->bigInteger('type_school')->nullable()->unsigned();
      $table->string('reset_password_employee', 255)->nullable()->comment('reset password user employee if forget the password');
      $table->string('reset_password_student', 255)->nullable()->comment('reset password user student if forget the password');
      $table->timestamps();

      $table->foreign('type_school')->references('id')->on('type_schools')
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
    Schema::dropIfExists('configurations');
  }
}
