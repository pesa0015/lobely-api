<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookAuthorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_authors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('book_id')->unsigned()->nullable();
            $table->integer('author_id')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::table('book_authors', function (Blueprint $table) {
            $table->foreign('book_id')->references('id')->on('books');
            $table->foreign('author_id')->references('id')->on('authors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('book_authors', function (Blueprint $table) {
            $table->dropForeign('book_authors_book_id_foreign');
            $table->dropForeign('book_authors_author_id_foreign');
        });

        Schema::dropIfExists('book_authors');
    }
}
