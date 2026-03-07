<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix: Settings model uses SoftDeletes but table lacks deleted_at
        if (!Schema::hasColumn('settings', 'deleted_at')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
