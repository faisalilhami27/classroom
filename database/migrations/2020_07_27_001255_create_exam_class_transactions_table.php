<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamClassTransactionsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('exam_class_transactions', function (Blueprint $table) {
      $table->id();
      $table->foreignId('exam_id')->constrained('manage_exams')->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('class_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
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
    Schema::dropIfExists('exam_class_transactions');
  }
}
