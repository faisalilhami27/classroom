<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('employees', function (Blueprint $table) {
      $table->id();
      $table->string('employee_identity_number', 20);
      $table->string('name', 70);
      $table->string('first_name', 70);
      $table->string('last_name', 70)->nullable();
      $table->string('email', 70)->unique();
      $table->string('phone_number', 15)->unique()->nullable();
      $table->string('photo')->nullable();
      $table->char('color', 7)->nullable();
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
    Schema::dropIfExists('employees');
  }
}
