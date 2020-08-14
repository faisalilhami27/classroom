<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserStudentsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('user_students', function (Blueprint $table) {
      $table->id();
      $table->bigInteger('student_id')->unsigned();
      $table->string('username', 20)->unique();
      $table->string('password');
      $table->integer('status')->comment('1 = active, 0 = inactive');
      $table->integer('email_verified')->nullable()->comment('1 = verified, 0 = not verified');
      $table->timestamps();
      $table->softDeletes();

      $table->foreign('student_id')->references('id')->on('students')
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
    Schema::dropIfExists('user_students');
  }
}
