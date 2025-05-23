<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CreateAdminAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin {admins*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create one or more admin accounts with hashed passwords';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $admins = $this->argument('admins');
        $insertData = [];

        $this->info("Processing " . count($admins) . " admin(s)...\n");

        foreach ($admins as $admin) {
            // Expected format: id,name
            $parts = explode(',', $admin);
            if (count($parts) !== 2) {
                $this->error("Invalid format: '$admin'. Expected format is id,name");
                continue;
            }

            [$id, $name] = $parts;

            $rawPassword = $id . 'admin';
            $hashedPassword = Hash::make($rawPassword);
            $email = $id . '@admin.gordoncollege.edu.ph';

            if (User::where('id', $id)->orWhere('email', $email)->exists()) {
                $this->warn("Admin with ID {$id} or email {$email} already exists. Skipping...");
                continue;
            }

            // Prepare for database insert
            $insertData[] = [
                'id' => $id,
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => 'admin', // Set role as admin
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $this->line("✔ Admin: $name | Email: $email | Raw Password: $rawPassword");
        }

        // Insert all admin accounts at once
        if (!empty($insertData)) {
            DB::table('users')->insert($insertData);
            
            $this->info("\nAdmin accounts created successfully!");
            $this->line("\nCreated " . count($insertData) . " admin account(s)");
        } else {
            $this->warn("No valid admin accounts to insert.");
        }

        return 0;
    }
}
