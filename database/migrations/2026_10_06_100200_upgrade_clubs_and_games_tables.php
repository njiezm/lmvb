<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->string('ffvb_number', 12)->nullable()->unique()->after('id');
            $table->string('short_name', 40)->nullable()->after('name');
            $table->string('city')->nullable()->after('description');
            $table->string('venue')->nullable()->after('city');
            $table->string('facebook')->nullable()->after('website');
            $table->string('instagram')->nullable()->after('facebook');
            $table->unsignedSmallInteger('founded_year')->nullable()->after('members_count');
            $table->softDeletes();

            $table->text('description')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->index(['active', 'name']);
        });

        Schema::table('games', function (Blueprint $table) {
            // Un club supprimé ne doit plus effacer son historique de matchs.
            $table->dropForeign(['home_team_id']);
            $table->dropForeign(['away_team_id']);
        });

        Schema::table('games', function (Blueprint $table) {
            $table->foreignId('competition_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('ffvb_code', 20)->nullable()->after('competition_id');
            $table->unsignedSmallInteger('matchday')->nullable()->after('ffvb_code');
            $table->string('home_team_name')->nullable()->after('home_team_id');
            $table->string('away_team_name')->nullable()->after('away_team_id');
            $table->json('set_scores')->nullable()->after('away_score');
            $table->unsignedSmallInteger('home_points')->nullable()->after('set_scores');
            $table->unsignedSmallInteger('away_points')->nullable()->after('home_points');
            $table->string('forfeit', 4)->nullable()->after('away_points'); // home | away | both
            $table->string('referee_1')->nullable()->after('forfeit');
            $table->string('referee_2')->nullable()->after('referee_1');
            $table->text('notes')->nullable()->after('referee_2');
            $table->boolean('locked')->default(false)->after('notes'); // saisie manuelle : l'import ne l'écrase pas
            $table->timestamp('synced_at')->nullable()->after('locked');

            $table->dateTime('date_time')->nullable()->change();
            $table->string('venue')->nullable()->change();
            $table->string('competition')->nullable()->default(null)->change();
            $table->unsignedBigInteger('home_team_id')->nullable()->change();
            $table->unsignedBigInteger('away_team_id')->nullable()->change();

            $table->foreign('home_team_id')->references('id')->on('clubs')->nullOnDelete();
            $table->foreign('away_team_id')->references('id')->on('clubs')->nullOnDelete();

            $table->unique(['competition_id', 'ffvb_code']);
            $table->index(['status', 'date_time']);
            $table->index('date_time');
        });

        // Table en doublon jamais utilisée (vide) : remplacée par « games ».
        Schema::dropIfExists('matches');
    }

    public function down(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_time');
            $table->string('venue');
            $table->foreignId('home_team_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('away_team_id')->constrained('clubs')->onDelete('cascade');
            $table->integer('home_score')->nullable();
            $table->integer('away_score')->nullable();
            $table->enum('status', ['scheduled', 'live', 'finished', 'cancelled'])->default('scheduled');
            $table->string('competition')->default('R1M');
            $table->timestamps();
        });

        Schema::table('games', function (Blueprint $table) {
            $table->dropForeign(['home_team_id']);
            $table->dropForeign(['away_team_id']);
            $table->dropUnique(['competition_id', 'ffvb_code']);
            $table->dropIndex(['status', 'date_time']);
            $table->dropIndex(['date_time']);
            $table->dropConstrainedForeignId('competition_id');
            $table->dropColumn(['ffvb_code', 'matchday', 'home_team_name', 'away_team_name', 'set_scores',
                'home_points', 'away_points', 'forfeit', 'referee_1', 'referee_2', 'notes', 'locked', 'synced_at']);
        });

        Schema::table('clubs', function (Blueprint $table) {
            $table->dropIndex(['active', 'name']);
            $table->dropUnique(['ffvb_number']);
            $table->dropColumn(['ffvb_number', 'short_name', 'city', 'venue', 'facebook', 'instagram', 'founded_year', 'deleted_at']);
        });
    }
};
