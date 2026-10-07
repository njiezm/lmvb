<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('club_id')->nullable()->after('role')->constrained()->nullOnDelete();
            $table->boolean('active')->default(true)->after('club_id');
            $table->timestamp('last_login_at')->nullable()->after('active');
            $table->index('role');
        });

        // Rôles : super_admin (gestion du site) / club_admin (gestion d'un club). Tout autre rôle n'a aucun accès.
        DB::table('users')->where('role', 'admin')->update(['role' => 'super_admin']);
        DB::table('users')->whereNotIn('role', ['super_admin', 'club_admin'])->update(['role' => 'user', 'active' => false]);

        if (! Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('club_id');
            $table->dropIndex(['role']);
            $table->dropColumn(['active', 'last_login_at']);
        });
        DB::table('users')->where('role', 'super_admin')->update(['role' => 'admin']);
        Schema::dropIfExists('password_reset_tokens');
    }
};
