<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostingsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('postings', function (Blueprint $table) {
      $table->id();
      $table->text('title');
      $table->dateTime('date');
      $table->integer('type_post')->comment('1 = announcement 2 = task');
      $table->foreignId('class_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('employee_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->foreignId('student_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
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
    Schema::dropIfExists('postings');
  }
}
