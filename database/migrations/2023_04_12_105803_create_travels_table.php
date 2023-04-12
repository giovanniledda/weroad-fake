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
        Schema::create('travels', function (Blueprint $table) {
            $table->id();

            $table->date('publicationDate')->nullable();
            $table->string('name', 140);
            $table->string('slug');
            $table->text('description');
            $table->smallInteger('days')->nullable();
            $table->smallInteger('nights')->virtualAs('days + 1')->nullable();
            $table->json('moods')->nullable();

            // Indexes
            $table->index('slug');

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
    }
};
