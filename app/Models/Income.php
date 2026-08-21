<?php

namespace App\Models;

use App\Observers\IncomeObserver;
use Carbon\Carbon;
use Database\Factories\IncomeFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

#[ObservedBy([IncomeObserver::class])]
class Income extends Model
{
    /** @use HasFactory<IncomeFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'amount',
        'description',
        'user_id',
        'received_on',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'received_on' => 'date',
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
        return $query->whereDate('received_on', now()->toDateString());
    }

    public function scopeOfDay(Builder $query, Carbon $day): Builder
    {
        return $query->whereDate('received_on', $day->toDateString());
    }

    public function scopeOfMonth(Builder $query, Carbon $carbon): Builder
    {
        return $query
            ->whereYear('received_on', $carbon->year)
            ->whereMonth('received_on', $carbon->month);
    }

    public function scopeMonthly(Builder $query): Builder
    {
        return $query
            ->whereYear('received_on', now()->year)
            ->whereMonth('received_on', now()->month);
    }

    public function isOwnedBy(?User $user): bool
    {
        return $user !== null && $this->user_id === $user->id;
    }
}
