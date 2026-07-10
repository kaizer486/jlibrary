<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class AddMediaTeamRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('🔄 Adding Media Team role and permissions...');

        // ==========================================
        // CREATE CONTENT MANAGEMENT PERMISSIONS
        // ==========================================
        
        $contentPermissions = [
            'manage hero slides',
            'manage news items',
            'manage founders',
            'manage site settings',
        ];

        $createdPermissions = [];
        foreach ($contentPermissions as $permission) {
            $perm = Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
            $createdPermissions[] = $perm->name;
            $this->command->info("   ✅ Permission '{$permission}' ready");
        }

        // ==========================================
        // CREATE OR UPDATE MEDIA TEAM ROLE
        // ==========================================
        
        $mediaTeam = Role::firstOrCreate([
            'name' => 'media_team',
            'guard_name' => 'web'
        ]);
        
        // Sync permissions (this will assign only these permissions)
        $mediaTeam->syncPermissions($contentPermissions);
        $this->command->info("   ✅ Role 'media_team' created/updated with " . count($contentPermissions) . " permissions");

        // ==========================================
        // ASSIGN CONTENT PERMISSIONS TO SUPER ADMIN
        // ==========================================
        
        $superAdmin = Role::where('name', 'super_admin')->where('guard_name', 'web')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($contentPermissions);
            $this->command->info("   ✅ Content permissions assigned to 'super_admin'");
        } else {
            $this->command->warn("   ⚠️ 'super_admin' role not found. Please create it first.");
        }

        // ==========================================
        // OPTIONAL: ASSIGN MEDIA TEAM ROLE TO A SPECIFIC USER
        // ==========================================
        
        // Uncomment and modify this section to assign the role to a specific user
        /*
        $userEmail = 'media@jlibrary.com'; // Change this to the actual email
        $user = \App\Models\User::where('email', $userEmail)->first();
        if ($user) {
            $user->assignRole('media_team');
            $this->command->info("   ✅ Media Team role assigned to '{$userEmail}'");
        } else {
            $this->command->warn("   ⚠️ User '{$userEmail}' not found. Skipping user assignment.");
        }
        */

        $this->command->info('✅ Media Team role and permissions added successfully!');
    }
}