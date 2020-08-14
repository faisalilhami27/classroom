<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnswerKeysTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('answer_keys', function (Blueprint $table) {
      $table->id();
      $table->text('answer_name');
      $table->integer('key');
      $table->bigInteger('question_id')->unsigned();
      $table->bigInteger('employee_id')->unsigned();
      $table->foreignId('created_by')->nullable()->constrained('employees')->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('last_updated_by')->nullable()->constrained('employees')->onUpdate('cascade')->onDelete('cascade');
      $table->timestamps();
      $table->softDeletes();

      $table->foreign('question_id')->references('id')->on('question_banks')
        ->onUpdate('cascade')
        ->onDelete('cascade');
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
    Schema::dropIfExists('answer_keys');
  }
}
