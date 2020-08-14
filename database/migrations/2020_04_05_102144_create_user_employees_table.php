<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserEmployeesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('user_employees', function (Blueprint $table) {
      $table->id();
      $table->bigInteger('employee_id')->unsigned();
      $table->string('username', 20)->unique();
      $table->string('password');
      $table->string('user_id_zoom', 255)->nullable();
      $table->integer('status_generate')->comment('1 = has been generated, 0 = has not been generated')->nullable();
      $table->integer('status')->comment('1 = active, 0 = inactive');
      $table->timestamps();
      $table->softDeletes();

      $table->foreign('employee_id')->references('id')->on('employees')
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
    Schema::dropIfExists('user_employees');
  }
}
