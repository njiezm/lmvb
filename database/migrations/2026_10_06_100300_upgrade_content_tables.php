<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['category_id']);
        });

        Schema::table('news', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('category_id')->references('id')->on('categories')->restrictOnDelete();
            $table->foreignId('club_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->string('image_credit')->nullable()->after('image');
            $table->string('source_url')->nullable()->after('image_credit');
            $table->unsignedInteger('views')->default(0)->after('featured');
            $table->index(['status', 'published_at']);
            $table->index('featured');
        });

        Schema::table('galleries', function (Blueprint $table) {
            $table->foreignId('club_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('type', 10)->default('image')->after('title'); // image | video
            $table->string('video_url')->nullable()->after('image');
            $table->string('credit')->nullable()->after('description');
            $table->index(['active', 'category']);
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->foreignId('club_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable()->after('read');
            $table->index(['read', 'created_at']);
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->string('coach')->nullable()->after('description');
            $table->index(['active', 'category']);
        });

        Schema::table('players', function (Blueprint $table) {
            $table->date('birth_date')->nullable()->change();
            $table->integer('number')->nullable()->change();
            $table->string('club_name')->nullable()->after('team_id');
        });

        Schema::table('beach_events', function (Blueprint $table) {
            $table->string('category', 20)->nullable()->after('type'); // M, F, mixte, jeunes
            $table->string('contact_email')->nullable()->after('registration_open');
            $table->index(['status', 'start_date']);
        });

        Schema::create('beach_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beach_event_id')->constrained()->cascadeOnDelete();
            $table->string('team_name');
            $table->string('player1_name');
            $table->string('player1_email');
            $table->string('player2_name');
            $table->string('player2_email');
            $table->string('phone', 20);
            $table->string('status', 20)->default('pending'); // pending, confirmed, cancelled
            $table->timestamps();
            $table->unique(['beach_event_id', 'team_name']);
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('token', 64)->unique();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('board_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role');
            $table->string('photo')->nullable();
            $table->text('bio')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(100);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category', 30)->default('general'); // reglement, formulaire, pv, calendrier, general
            $table->string('file')->nullable();
            $table->string('url')->nullable();
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('url')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(100);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partners');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('board_members');
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('beach_registrations');

        Schema::table('beach_events', function (Blueprint $table) {
            $table->dropIndex(['status', 'start_date']);
            $table->dropColumn(['category', 'contact_email']);
        });
        Schema::table('players', fn (Blueprint $table) => $table->dropColumn('club_name'));
        Schema::table('teams', function (Blueprint $table) {
            $table->dropIndex(['active', 'category']);
            $table->dropColumn('coach');
        });
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex(['read', 'created_at']);
            $table->dropConstrainedForeignId('club_id');
            $table->dropColumn('ip_address');
        });
        Schema::table('galleries', function (Blueprint $table) {
            $table->dropIndex(['active', 'category']);
            $table->dropConstrainedForeignId('club_id');
            $table->dropColumn(['type', 'video_url', 'credit']);
        });
        Schema::table('news', function (Blueprint $table) {
            $table->dropIndex(['status', 'published_at']);
            $table->dropIndex(['featured']);
            $table->dropConstrainedForeignId('club_id');
            $table->dropColumn(['image_credit', 'source_url', 'views']);
        });
    }
};
