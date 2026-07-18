<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\ExpenseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Expense extends Model
{
    /** @use HasFactory<ExpenseFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'description',
        'user_id',
        'spent_on',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'user_id' => 'integer',
            'spent_on' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCurrentUser(Builder $query): Builder
    {
        return $query->where('user_id', Auth::id());
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('spent_on', now()->toDateString());
    }

    public function scopeOfDay(Builder $query, Carbon $day): Builder
    {
        return $query->whereDate('spent_on', $day->toDateString());
    }

    public function scopeMonthly(Builder $query): Builder
    {
        return $query
            ->whereYear('spent_on', now()->year)
            ->whereMonth('spent_on', now()->month);
    }

    public function isOwnedBy(?User $user): bool
    {
        return $user !== null && (int) $this->user_id === (int) $user->id;
    }
}
