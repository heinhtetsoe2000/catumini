<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ExpenseCategory extends Model
{
    use HasUuids;

    protected $fillable = ['name', 'color', 'icon'];

    public static function normalizeName(string $name): string
    {
        return Str::lower(trim($name));
    }

    protected static function booted(): void
    {
        static::saving(function (ExpenseCategory $category): void {
            $category->name = trim($category->name);
            $category->name_key = self::normalizeName($category->name);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'category_id');
    }
}
