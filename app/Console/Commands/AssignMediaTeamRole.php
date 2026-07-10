<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AssignMediaTeamRole extends Command
{
    protected $signature = 'role:assign-media-team {email : Email of the user to assign media team role}';
    protected $description = 'Assign Media Team role to a user';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return 1;
        }

        if ($user->isMediaTeam()) {
            $this->info("User '{$email}' already has Media Team role.");
            return 0;
        }

        $user->assignRole('media_team');
        $this->info("✅ Media Team role assigned to '{$email}' successfully!");
        return 0;
    }
}