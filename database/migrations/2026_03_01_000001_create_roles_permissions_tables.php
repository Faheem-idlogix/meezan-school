<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Permissions ──
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();           // e.g. students.view
            $table->string('display_name');              // e.g. View Students
            $table->string('module');                    // e.g. students
            $table->string('group')->nullable();         // e.g. Academic
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // ── Roles ──
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();            // e.g. admin
            $table->string('display_name');               // e.g. Administrator
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false); // system roles can't be deleted
            $table->timestamps();
        });

        // ── Role ↔ Permission Pivot ──
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->unique(['role_id', 'permission_id']);
        });

        // ── User ↔ Role Pivot ──
        Schema::create('user_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->unique(['user_id', 'role_id']);
        });

        // ── User ↔ Permission Pivot (direct/override) ──
        Schema::create('user_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->unique(['user_id', 'permission_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_permission');
        Schema::dropIfExists('user_role');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
    }
};
