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
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('main_code');
            $table->string('secondary_code')->nullable();
            $table->string('name');
        });

        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['atom', 'rss']);
            $table->string('xml_source');
            $table->string('md5_checksum');
            $table->integer('ttl')->default(60);
            $table->timestamps();
        });

        Schema::create('rss_channels', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('link');
            $table->string('description');
            $table->foreignId('language_id')->nullable()->constrained();
            $table->dateTimeTz('pub_date')->nullable();
            $table->dateTimeTz('last_build_date')->nullable();
            $table->string('image')->nullable();
            $table->foreignId('channel_id')->nullable()->constrained();
            $table->timestamps();
        });

        Schema::create('atom_feeds', function (Blueprint $table) {
            $table->id();
            $table->string('atom_id')->unique();
            $table->string('title');
            $table->dateTimeTz('updated');
            $table->string('self_link')->nullable();
            $table->string('alternate_link')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('icon')->nullable();
            $table->string('logo')->nullable();
            $table->foreignId('channel_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('languages');
        Schema::dropIfExists('rss_channels');
        Schema::dropIfExists('atom_feeds');
        Schema::dropIfExists('channels');
    }
};
