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
            $table->string('guid'); // Required in Atom (as "id").
            $table->string('title');
            $table->text('description')->nullable(); // Required in RSS. Optional in Atom (as "subtitle").
            $table->string('link'); // Required in RSS. Recommended in Atom.
            $table->dateTimeTz('last_build_date')->nullable(); // Required in Atom (as "updated").
            $table->dateTimeTz('pub_date')->nullable(); // Does not exist in Atom (use "updated").
            $table->foreignId('language_id')->nullable()->constrained(); // Does not exist in Atom.
            $table->enum('type', ['atom', 'rss']);
            $table->string('xml_source');
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
        Schema::dropIfExists('channels');
    }
};

