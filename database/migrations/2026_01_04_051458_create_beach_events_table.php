<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('beach_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('image')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('location');
            $table->enum('type', ['tournament', 'training', 'exhibition'])->default('tournament');
            $table->enum('status', ['upcoming', 'ongoing', 'finished', 'cancelled'])->default('upcoming');
            $table->integer('max_teams')->nullable();
            $table->integer('registered_teams')->default(0);
            $table->decimal('prize_pool', 10, 2)->nullable();
            $table->boolean('registration_open')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('beach_events');
    }
};