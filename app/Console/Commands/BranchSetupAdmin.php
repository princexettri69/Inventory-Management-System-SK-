<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class BranchSetupAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'branch:setup-admin {--email=} {--password=} {--name=}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Setup the admin user for a specific branch';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email') ?? 'admin@sktrade.com';
        $password = $this->option('password') ?? 'password';
        $name = $this->option('name') ?? 'Branch Admin';

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'role' => 'admin', // Assuming role field exists from migration
            ]
        );

        $this->info("Admin user created/updated: {$email}");
        return 0;
    }
}
