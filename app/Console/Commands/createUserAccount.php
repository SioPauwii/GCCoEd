<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CreateUserAccount extends Command
{
    protected $signature = 'make:users {users*}';
    protected $description = 'Create one or more users with hashed passwords and generate a single SQL command';

    public function handle()
    {
        $users = $this->argument('users');
        $insertData = [];
        $sqlValues = [];

        $this->info("Processing " . count($users) . " user(s)...\n");

        foreach ($users as $user) {
            // Expected format: id,name,email
            $parts = explode(',', $user);
            if (count($parts) !== 2) {
                $this->error("Invalid format: '$user'. Expected format is id,name");
                continue;
            }

            [$id, $name] = $parts;

            $rawPassword = $id . 'password';
            $hashedPassword = Hash::make($rawPassword);
            $email = $id . '@gordoncollege.edu.ph';

            if (User::where('id', $id)->orWhere('email', $email)->exists()) {
                $this->warn("User with ID {$id} or email {$email} already exists. Skipping...");
                continue; // Skip this user and move on to the next one
            }

            // Prepare for database insert
            $insertData[] = [
                'id' => $id,
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Prepare SQL preview part
            $escapedName = addslashes($name);
            $escapedEmail = addslashes($email);
            $sqlValues[] = "($id, '$escapedName', '$escapedEmail')";

            $this->line("✔ User: $name | Raw Password: $rawPassword");
        }

        // Insert all users at once
        if (!empty($insertData)) {
            DB::table('users')->insert($insertData);

            $sql = "INSERT INTO users (id, name, email) VALUES \n" . implode(",\n", $sqlValues) . ";";
            $this->info("\nUsers created successfully.");
            $this->line("\nSQL Preview:\n$sql");
        } else {
            $this->warn("No valid users to insert.");
        }

        return 0;
    }
}
