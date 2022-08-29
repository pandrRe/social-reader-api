<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rss_items', function (Blueprint $table) {
            $table->id();
            $table->text('title')->nullable();
            $table->text('link')->nullable();
            $table->text('description')->nullable();
            $table->string('author')->nullable();
            $table->string('comments')->nullable();
            $table->text('guid')->nullable();
            $table->string('source')->nullable();
            $table->dateTimeTz('pub_date')->nullable();
            $table->foreignId('channel_id')->nullable()->constrained();
            $table->unique(['channel_id', 'title', 'description']);
            $table->unique(['channel_id', 'link']);
            $table->unique(['channel_id', 'guid']);
            $table->timestamps();
        });

        Schema::create('atom_entries', function (Blueprint $table) {
            $table->id();
            $table->string('atom_id');
            $table->string('title');
            $table->dateTimeTz('updated');
            $table->string('author')->nullable();
            $table->text('content')->nullable();
            $table->string('summary')->nullable();
            $table->string('link')->nullable();
            $table->foreignId('channel_id')->nullable()->constrained();
            $table->unique(['channel_id', 'atom_id']);
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
        Schema::dropIfExists('rss_items');
        Schema::dropIfExists('atom_entries');
    }
};
