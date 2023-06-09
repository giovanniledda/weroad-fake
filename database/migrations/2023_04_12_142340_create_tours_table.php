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
        Schema::create('tours', function (Blueprint $table) {
            $table->id();

            $table->string('name', 140);
            $table->date('startingDate')->nullable();
            $table->date('endingDate')->nullable();
            $table->integer('price')->nullable();

            // Relationship: Travel
            $table->foreignId('travelId')->constrained('travels')->onDelete('cascade');

            // Indexes
            $table->index(['price', 'startingDate']);
            $table->uuid('uuid')->unique();

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
