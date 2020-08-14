<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamRulesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('exam_rules', function (Blueprint $table) {
      $table->id();
      $table->string('name', 100);
      $table->text('text');
      $table->foreignId('employee_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('created_by')->nullable()->constrained('employees')->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('last_updated_by')->nullable()->constrained('employees')->onUpdate('cascade')->onDelete('cascade');
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
    Schema::dropIfExists('exam_rules');
  }
}
