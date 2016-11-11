<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('author');
            $table->integer('author_id');
            $table->mediumText('author_bio');
            $table->string('authors');
            $table->string('title_slug');
            $table->string('author_slug');
            $table->bigInteger('isbn13');
            $table->string('isbn10');
            $table->string('price');
            $table->string('format');
            $table->string('publisher');
            $table->string('pubdate');
            $table->string('edition');
            $table->string('subjects');
            $table->string('lexile');
            $table->string('pages');
            $table->string('dimensions');
            $table->mediumText('overview');
            $table->text('excerpt');
            $table->mediumText('synopsis');
            $table->text('toc');
            $table->mediumText('editorial_reviews');
            $table->string('cover')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
}
