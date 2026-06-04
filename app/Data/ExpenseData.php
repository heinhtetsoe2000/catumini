<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ExpenseData extends Data
{
    public function __construct(
        public string $name,
        public int $amount,
    ) {}
}
