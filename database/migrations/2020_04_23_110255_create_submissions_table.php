<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubmissionsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('submissions', function (Blueprint $table) {
      $table->id('id');
      //$table->increments('id');
      //$table->primary(array('id'));
      
      $table->bigInteger('userid')->unsigned()->index();
      $table->foreign('userid')->references('id')->on('users')->onDelete('cascade');
      
      $table->text('data');

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
      Schema::dropIfExists('submissions');
  }
}
