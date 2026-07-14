<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owner = User::query()->where('email', env('OWNER_EMAIL', 'owner@example.com'))->first();

        if ($owner === null) {
            return;
        }

        Expense::factory()->count(10)->create([
            'user_id' => $owner->id,
        ]);
    }
}
