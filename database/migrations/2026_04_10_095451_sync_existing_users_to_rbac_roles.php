<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Sync all existing users' legacy role column to RBAC user_role pivot table.
     * Users who already have RBAC roles are skipped.
     */
    public function up(): void
    {
        $roleMap = [
            'super_admin' => 'super_admin',
            'admin'       => 'admin',
            'teacher'     => 'teacher',
            'accountant'  => 'accountant',
            'student'     => 'student',
            'receptionist'=> 'receptionist',
        ];

        $roles = DB::table('roles')->pluck('id', 'name');

        $users = DB::table('users')
            ->whereNotNull('role')
            ->where('role', '!=', '')
            ->select('id', 'role')
            ->get();

        foreach ($users as $user) {
            $rbacRoleName = $roleMap[$user->role] ?? null;

            if ($rbacRoleName && isset($roles[$rbacRoleName])) {
                $exists = DB::table('user_role')
                    ->where('user_id', $user->id)
                    ->where('role_id', $roles[$rbacRoleName])
                    ->exists();

                if (!$exists) {
                    DB::table('user_role')->insert([
                        'user_id' => $user->id,
                        'role_id' => $roles[$rbacRoleName],
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        // Not reversible - removing roles could break access
    }
};
