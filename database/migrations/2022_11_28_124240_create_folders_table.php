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
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('parent_folder_id')
                ->constrained('folders')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained();
            $table->timestamps();
        });

        Schema::table('channel_subscriptions', function (Blueprint $table) {
            $table->foreignId('folder_id')
                ->nullable()
                ->constrained('folders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('folders');
        Schema::table('channel_subscriptions', function (Blueprint $table) {
            $table->dropForeign('channel_subscriptions_folder_id_foreign');
        });
    }
};
