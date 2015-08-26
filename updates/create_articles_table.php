<?php namespace Abnmt\TheaterPress\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateArticlesTable extends Migration
{

    public function up()
    {
        Schema::dropIfExists('abnmt_theaterpress_articles');
        Schema::create('abnmt_theaterpress_articles', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('title');
            $table->string('slug')->index();
            $table->text('content')->nullable()->default(null);

            $table->string('author')->nullable()->default(null);
            $table->string('source')->nullable()->default(null);
            $table->string('source_link')->nullable()->default(null);
            $table->datetime('source_date')->nullable()->default(null);

            $table->datetime('published_at')->nullable()->default(null);
            $table->boolean('published')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('abnmt_theaterpress_articles');
    }

}
