<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCinemaSchema extends Migration
{
    /** ToDo: Create a migration that creates all tables for the following user stories
    For an example on how a UI for an api using this might look like, please try to book a show at https://in.bookmyshow.com/.
    To not introduce additional complexity, please consider only one cinema.
    Please list the tables that you would create including keys, foreign keys and attributes that are required by the user stories.
    ## User Stories
    **Movie exploration**
    * As a user I want to see which films can be watched and at what times
    * As a user I want to only see the shows which are not booked out
    **Show administration**
    * As a cinema owner I want to run different films at different times
    * As a cinema owner I want to run multiple films at the same time in different showrooms
    **Pricing**
    * As a cinema owner I want to get paid differently per show
    * As a cinema owner I want to give different seat types a percentage premium, for example 50 % more for vip seat
    **Seating**
    * As a user I want to book a seat
    * As a user I want to book a vip seat/couple seat/super vip/whatever
    * As a user I want to see which seats are still available
    * As a user I want to know where I'm sitting on my ticket
    * As a cinema owner I dont want to configure the seating for every show
    */
    public function up()
    {
        Schema::create('cinemas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('duration');
            $table->foreignId('cinema_id')->constrained()->onDelete('cascade');
            $table->timestamp('onAir');
            $table->enum('status', ["Pre-release", "open", "booked"]);
            $table->text('description');
            $table->timestamps();
        });
        Schema::create('cast', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('image')->nullable();
            $table->bigInteger('cinema_id');
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['cast', 'crew']);
            $table->timestamps();
        });
        Schema::create('timings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->timestamp('on_air_time');
            $table->enum('type', ['cast', 'crew']);
            $table->timestamps();
        });
        Schema::create('pricing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->foreignId('timing_id')->constrained()->onDelete('cascade');
            $table->integer('price');
            $table->integer('vip_seat_premium')->nullable()->default(0);
            $table->timestamps();
        });
        Schema::create('show_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('cinema_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->foreignId('timing_id')->constrained()->onDelete('cascade');
            $table->integer('price');
            $table->integer('vip_seat_premium')->nullable()->default(0);
            $table->foreignId('show_room_id')->constrained()->onDelete('cascade');
            $table->boolean('is_available')->default(true);
            $table->string('seat_type')->nullable();
            $table->timestamps();
        });
        Schema::create('user_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // TODD: create user and make foreign key
            $table->foreignId('seat_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('total');
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
}