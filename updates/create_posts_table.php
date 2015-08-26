<?php namespace Abnmt\TheaterPress\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreatePostsTable extends Migration
{

    public function up()
    {
        Schema::dropIfExists('abnmt_theaterpress_posts');
        Schema::create('abnmt_theaterpress_posts', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('title');
            $table->string('slug')->index();
            $table->text('content')->nullable()->default(null);
            $table->text('excerpt')->nullable()->default(null);

            $table->datetime('published_at')->nullable()->default(null);
            $table->boolean('published')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('abnmt_theaterpress_posts');
    }

}
