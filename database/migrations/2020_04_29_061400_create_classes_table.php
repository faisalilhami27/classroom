<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('classes', function (Blueprint $table) {
      $table->id();
      $table->char('code', 7);
      $table->string('class_order', 5);
      $table->string('image', 255)->nullable();
      $table->char('color', 7)->nullable();
      $table->foreignId('major_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('grade_level_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('semester_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('subject_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('employee_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('school_year_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
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
    Schema::dropIfExists('classes');
  }
}
