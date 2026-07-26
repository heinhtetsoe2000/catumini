<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if ($this->alreadyUsesUuidPrimaryKeys()) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->uuid('uuid')->nullable()->after('id');
        });

        Schema::table('expenses', function (Blueprint $table): void {
            $table->uuid('uuid')->nullable()->after('id');
            $table->uuid('user_uuid')->nullable()->after('user_id');
        });

        $userMap = $this->backfillUserUuids();
        $this->backfillExpenseUuids($userMap);

        Schema::disableForeignKeyConstraints();

        Schema::create('users_new', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        foreach (DB::table('users')->orderBy('id')->get() as $user) {
            DB::table('users_new')->insert([
                'id' => $user->uuid,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'password' => $user->password,
                'remember_token' => $user->remember_token,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]);
        }

        Schema::drop('users');
        Schema::rename('users_new', 'users');

        Schema::create('expenses', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->integer('amount');
            $table->string('description')->nullable();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('spent_on');
            $table->timestamps();
        });

        foreach (DB::table('expenses_legacy')->orderBy('id')->get() as $expense) {
            DB::table('expenses')->insert([
                'id' => $expense->uuid,
                'name' => $expense->name,
                'amount' => $expense->amount,
                'description' => $expense->description,
                'user_id' => $expense->user_uuid,
                'spent_on' => $expense->spent_on,
                'created_at' => $expense->created_at,
                'updated_at' => $expense->updated_at,
            ]);
        }

        Schema::drop('expenses_legacy');

        if (Schema::hasTable('sessions') && Schema::hasColumn('sessions', 'user_id')) {
            Schema::table('sessions', function (Blueprint $table): void {
                $table->uuid('user_id')->nullable()->change();
            });
        }

        Schema::enableForeignKeyConstraints();

        DB::table('sessions')->truncate();
        Cache::flush();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new RuntimeException('UUID v7 migration cannot be reversed without a database backup.');
    }

    /**
     * @return array<int, string>
     */
    private function backfillUserUuids(): array
    {
        $userMap = [];

        foreach (DB::table('users')->orderBy('id')->get() as $user) {
            $uuid = (string) Str::uuid7();
            $userMap[(int) $user->id] = $uuid;

            DB::table('users')->where('id', $user->id)->update(['uuid' => $uuid]);
        }

        return $userMap;
    }

    /**
     * @param  array<int, string>  $userMap
     */
    private function backfillExpenseUuids(array $userMap): void
    {
        $soleOwnerUuid = count($userMap) === 1 ? reset($userMap) : null;

        Schema::rename('expenses', 'expenses_legacy');

        foreach (DB::table('expenses_legacy')->orderBy('id')->get() as $expense) {
            $userUuid = null;

            if ($expense->user_id !== null) {
                $userUuid = $userMap[(int) $expense->user_id] ?? null;
            }

            if ($userUuid === null) {
                if ($soleOwnerUuid !== null) {
                    $userUuid = $soleOwnerUuid;
                } else {
                    throw new RuntimeException(
                        'Expense row '.$expense->id.' has no resolvable Owner before UUID migration.'
                    );
                }
            }

            DB::table('expenses_legacy')->where('id', $expense->id)->update([
                'uuid' => (string) Str::uuid7(),
                'user_uuid' => $userUuid,
            ]);
        }
    }

    private function alreadyUsesUuidPrimaryKeys(): bool
    {
        if (! Schema::hasTable('users')) {
            return false;
        }

        return in_array(Schema::getColumnType('users', 'id'), ['char', 'varchar', 'uuid'], true);
    }
};
