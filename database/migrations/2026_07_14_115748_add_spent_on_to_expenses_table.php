<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->date('spent_on')->default('1970-01-01')->after('user_id');
        });

        DB::table('expenses')->orderBy('id')->each(function (object $expense): void {
            DB::table('expenses')->where('id', $expense->id)->update([
                'spent_on' => Carbon::parse($expense->created_at)
                    ->timezone(config('app.timezone'))
                    ->toDateString(),
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('spent_on');
        });
    }
};
