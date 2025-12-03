<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserSession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSessionSeeder extends Seeder
{
    public function run(): void
    {
        // Get all users
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->error('No users found. Please run UserSeeder first.');
            return;
        }

        // Create sample sessions for each user
        foreach ($users as $user) {
            // Create 1-3 sessions per user
            $sessionCount = rand(1, 3);
            
            for ($i = 0; $i < $sessionCount; $i++) {
                $isActive = $i === 0; // First session is active
                
                UserSession::create([
                    'user_id' => $user->id,
                    'session_id' => Str::random(40),
                    'ip_address' => fake()->ipv4(),
                    'user_agent' => fake()->userAgent(),
                    'login_at' => fake()->dateTimeBetween('-30 days', now()),
                    'logout_at' => $isActive ? null : fake()->dateTimeBetween('-1 day', now()),
                    'last_activity_at' => $isActive ? now() : fake()->dateTimeBetween('-30 days', '-1 day'),
                    'is_active' => $isActive,
                ]);
            }
        }

        $this->command->info('User sessions created successfully!');
        $this->command->info('Created ' . UserSession::count() . ' sessions');
        $this->command->info('Active sessions: ' . UserSession::where('is_active', true)->count());
        
        // Display session statistics
        $this->command->info('Session Statistics:');
        $this->command->info('  - Active sessions: ' . UserSession::where('is_active', true)->count());
        $this->command->info('  - Inactive sessions: ' . UserSession::where('is_active', false)->count());
        $this->command->info('  - Sessions today: ' . UserSession::whereDate('login_at', today())->count());
    }
}
