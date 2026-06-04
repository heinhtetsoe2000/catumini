<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">

        <title>{{ config('app.name', 'Mimi') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    </head>
    <body>
        <h1 class="text-3xl font-bold underline">
            {{ number_format($total) }}
        </h1>
        <p class="text-sm text-gray-500">
            {{ now()->format('M d, Y') }}
        </p>
        <div class="container">
            <div class="card">
                <p class="title">{{ number_format($total) }}</p>
                <p class="subtitle">{{ now()->format('M d, Y') }}</p>
            </div>

            <div class="card expenses-list">
                @forelse ($expenses as $expense)
                    <div class="expense-item">
                        <span class="expense-item-name">{{ $expense->name }}</span>
                        <span class="expense-item-amount">{{ number_format($expense->amount) }}</span>
                    </div>
                @empty
                    <p class="no-expenses-message">No expenses yet</p>
                @endforelse
            </div>

            <a href="/add-expense">
                <button class="add-expense-button" type="button">
                    +
                </button>
            </a>

            <native:bottom-nav>
                <native:bottom-nav-item
                    id="home"
                    icon="house.fill"
                    label="Home"
                    url="/"
                    :active="true"
                />
                <native:bottom-nav-item
                    id="add-expense"
                    icon="plus.circle.fill"
                    label="Add Expense"
                    url="/add-expense"
                    :active="request()->is('add-expense*')"
                />
                <native:bottom-nav-item
                    id="about"
                    icon="info.circle.fill"
                    label="About"
                    url="/about"
                    :active="request()->is('about*')"
                />
                <native:bottom-nav-item
                    id="settings"
                    icon="gearshape.fill"
                    label="Settings"
                    url="/settings"
                    :active="request()->is('settings*')"
                />
            </native:bottom-nav>
        </div>
    </body>
</html>
