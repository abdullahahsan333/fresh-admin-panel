<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropUnique('servers_ip_unique');
            $table->unique(['project_id', 'ip'], 'servers_project_id_ip_unique');
        });
    }

    public function down(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropUnique('servers_project_id_ip_unique');
            $table->unique('ip', 'servers_ip_unique');
        });
    }
};

