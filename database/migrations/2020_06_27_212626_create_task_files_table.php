<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskFilesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('task_files', function (Blueprint $table) {
      $table->id();
      $table->foreignId('task_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
      $table->string('file', 255)->nullable();
      $table->string('filename', 255)->nullable();
      $table->string('mime_type', 255)->nullable();
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
    Schema::dropIfExists('task_files');
  }
}
