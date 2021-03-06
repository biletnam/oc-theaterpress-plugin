<?php namespace Abnmt\TheaterPress\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateCategoriesTable extends Migration
{

    public function up()
    {

        Schema::dropIfExists('abnmt_theaterpress_categories');
        Schema::dropIfExists('abnmt_theaterpress_articles_categories');

        Schema::create('abnmt_theaterpress_categories', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('name');
            $table->string('slug')->index();
            $table->string('code')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();
        });

        Schema::create('abnmt_theaterpress_articles_categories', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('article_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->primary(['article_id', 'category_id'], 'article_category');
        });
    }

    public function down()
    {
        Schema::dropIfExists('abnmt_theaterpress_categories');
        Schema::dropIfExists('abnmt_theaterpress_articles_categories');
    }

}
