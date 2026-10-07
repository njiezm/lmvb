<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->string('name', 9)->unique(); // ex. 2026/2027 (format FFVolley)
            $table->string('slug', 9)->unique(); // ex. 2026-2027
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->boolean('is_current')->default(false)->index();
            $table->timestamps();
        });

        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->constrained()->cascadeOnDelete();
            $table->string('code', 10);                 // code poule FFVolley (CUM, PUF…)
            $table->string('name');
            $table->string('slug');
            $table->string('group_name')->nullable();   // ex. CHAMPIONNAT SENIOR MASCULIN 2025/2026
            $table->string('category', 20)->default('senior'); // senior, m21, m18, m15, m13, beach, autre
            $table->char('gender', 1)->nullable();      // M, F, X
            $table->string('phase', 20)->default('regular'); // regular, playoff, playdown, cup, tournament
            $table->boolean('is_ffvb')->default(true);  // synchronisée depuis la FFVolley
            $table->boolean('active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(100);
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->unique(['season_id', 'code']);
            $table->unique(['season_id', 'slug']);
            $table->index(['active', 'sort_order']);
        });

        Schema::create('standings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('club_id')->nullable()->constrained()->nullOnDelete();
            $table->string('team_name');
            $table->unsignedSmallInteger('position');
            $table->smallInteger('points')->default(0);
            $table->unsignedSmallInteger('played')->default(0);
            $table->unsignedSmallInteger('won')->default(0);
            $table->unsignedSmallInteger('lost')->default(0);
            $table->unsignedSmallInteger('forfeits')->default(0);
            $table->unsignedSmallInteger('w30')->default(0);
            $table->unsignedSmallInteger('w31')->default(0);
            $table->unsignedSmallInteger('w32')->default(0);
            $table->unsignedSmallInteger('l23')->default(0);
            $table->unsignedSmallInteger('l13')->default(0);
            $table->unsignedSmallInteger('l03')->default(0);
            $table->unsignedSmallInteger('sets_for')->default(0);
            $table->unsignedSmallInteger('sets_against')->default(0);
            $table->unsignedInteger('points_for')->default(0);
            $table->unsignedInteger('points_against')->default(0);
            $table->timestamps();

            $table->unique(['competition_id', 'team_name']);
            $table->index(['competition_id', 'position']);
        });

        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('source', 30)->default('ffvb');
            $table->string('trigger', 20)->default('cron'); // cron, visit, manual
            $table->string('season', 9)->nullable();
            $table->string('status', 20)->default('running'); // running, success, partial, failed
            $table->unsignedInteger('competitions_count')->default(0);
            $table->unsignedInteger('games_created')->default(0);
            $table->unsignedInteger('games_updated')->default(0);
            $table->unsignedInteger('clubs_created')->default(0);
            $table->text('message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'finished_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
        Schema::dropIfExists('standings');
        Schema::dropIfExists('competitions');
        Schema::dropIfExists('seasons');
    }
};
