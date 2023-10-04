<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*
         * id
         * type
         * name
         * description|null
         * rating.kp|null
         * votes.kp|null
         * poster.url|null
         * backdrop.url|null
         * year|null
         * ageRating|null
         * isSeries
         * movieLength|null
         * seriesLength|null
         * totalSeriesLength|null
         *
         * RELATIONS
         * ?videos.trailers
         * genres
         * countries
         */

        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kp_id');
            $table->string('type', 63)->index();
            $table->string('name', 511)->index();
            $table->text('description')->nullable();
            $table->decimal('kp_rating', 5, 3)->nullable();
            $table->integer('kp_votes_count')->nullable();
            $table->string('poster_url', 1023)->nullable();
            $table->string('backdrop_url', 1023)->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->unsignedTinyInteger('age_rating')->nullable();
            $table->boolean('is_series');
            $table->unsignedSmallInteger('movie_length')->nullable();
            $table->unsignedSmallInteger('series_length')->nullable();
            $table->unsignedMediumInteger('total_series_length')->nullable();
            $table->dateTimes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
