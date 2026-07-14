<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create
                            {--email= : The user email address}
                            {--password= : The user password}
                            {--name= : The user display name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a user with email and password';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->option('email');
        $password = $this->option('password');
        $name = $this->option('name');

        $validator = Validator::make(
            [
                'email' => $email,
                'password' => $password,
                'name' => $name,
            ],
            [
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'string', Password::defaults()],
                'name' => ['nullable', 'string', 'max:255'],
            ],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        /** @var array{email: string, password: string, name: string|null} $data */
        $data = $validator->validated();

        $resolvedName = filled($data['name'] ?? null)
            ? (string) $data['name']
            : (str($data['email'])->before('@')->toString() ?: 'Owner');

        $user = User::query()->create([
            'name' => $resolvedName,
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $user->forceFill(['email_verified_at' => now()])->save();

        $this->info("User [{$user->email}] created successfully.");

        return self::SUCCESS;
    }
}
