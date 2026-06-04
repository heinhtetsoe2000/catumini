<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Mimi') }} - Built with NativePHP</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles -->
        <style>
                *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
                html { font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; line-height: 1.5; }
                body { min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; background: linear-gradient(135deg, #fafafa 0%, #f0f0f0 100%); }
                @media (prefers-color-scheme: dark) {
                    body { background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%); color: #ededec; }
                }
                .container { max-width: 28rem; width: 100%; }
                .card { background: white; border-radius: 1rem; padding: 2.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); }
                @media (prefers-color-scheme: dark) {
                    .card { background: #161615; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3); }
                }
                .logo { width: 4rem; height: 4rem; margin: 0 auto 1.5rem; }
                .title { font-size: 1.5rem; font-weight: 600; text-align: center; margin-bottom: 0.5rem; color: #1b1b18; }
                @media (prefers-color-scheme: dark) { .title { color: #ededec; } }
                .subtitle { font-size: 0.875rem; text-align: center; color: #706f6c; margin-bottom: 2rem; }
                @media (prefers-color-scheme: dark) { .subtitle { color: #a1a09a; } }
                .expenses-list { margin-top: 1rem; }
                .expense-item { font-size: 0.875rem; text-align: left; color: #706f6c; margin-bottom: 0.5rem; display: flex; justify-content: space-between; }
                @media (prefers-color-scheme: dark) { .expense-item { color: #a1a09a; } }
                .expense-item-name { font-size: 0.875rem; display: inline-block; text-align: left; color: #706f6c; margin-bottom: 0.5rem; }
                @media (prefers-color-scheme: dark) { .expense-item-name { color: #a1a09a; } }
                .expense-item-amount { font-size: 0.875rem; display: inline-block; text-align: right; color: #706f6c; margin-bottom: 0.5rem; }
                @media (prefers-color-scheme: dark) { .expense-item-amount { color: #a1a09a; } }
                .add-expense-button { width: 2rem; height: 2rem; border-radius: 50%; background: #1b1b18; color: #ededec; font-size: 1rem; font-weight: 600; text-align: center; line-height: 2rem; cursor: pointer; border: none; position: fixed; bottom: 2rem; right: 2rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); transition: all 0.15s; }
                @media (prefers-color-scheme: dark) { .add-expense-button { background: #ededec; color: #1b1b18; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3); } }
                .add-expense-button:hover { background: #262625; color: #ededec; }
                .no-expenses-message { font-size: 0.875rem; text-align: center; color: #706f6c; margin-bottom: 0.5rem; }
                @media (prefers-color-scheme: dark) { .no-expenses-message { color: #a1a09a; } }
                @media (prefers-color-scheme: dark) { .add-expense-button:hover { background: #1f1f1e; color: #ededec; } }
                .links { display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 2rem; }
                .link { display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1rem; border-radius: 0.5rem; text-decoration: none; color: #1b1b18; background: #fafafa; border: 1px solid #e5e5e5; transition: all 0.15s; font-size: 0.875rem; }
                .link:hover { border-color: #d1d1d1; background: #f5f5f5; }
                @media (prefers-color-scheme: dark) {
                    .link { background: #1f1f1e; border-color: #3e3e3a; color: #ededec; }
                    .link:hover { border-color: #525250; background: #262625; }
                }
                .link-icon { width: 1.25rem; height: 1.25rem; flex-shrink: 0; }
                .link-text { flex: 1; }
                .link-arrow { width: 1rem; height: 1rem; opacity: 0.5; }
                .divider { height: 1px; background: #e5e5e5; margin: 1.5rem 0; }
                @media (prefers-color-scheme: dark) { .divider { background: #3e3e3a; } }
                .footer { text-align: center; font-size: 0.75rem; color: #a1a09a; }
                .footer a { color: inherit; text-decoration: underline; text-underline-offset: 2px; }
                .footer a:hover { color: #706f6c; }
                @media (prefers-color-scheme: dark) { .footer a:hover { color: #d1d1d1; } }
                .badge { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; border-radius: 9999px; background: linear-gradient(135deg, #7c3aed 0%, #db2777 100%); color: white; font-size: 0.625rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="card">
                <p class="title">Add Expense</p>
                <p class="subtitle">{{ now()->format('M d, Y') }}</p>
            </div>

            <native:bottom-nav>
                <native:bottom-nav-item
                    id="home"
                    icon="house.fill"
                    label="Home"
                    url="/"
                    :active="false"
                />
                <native:bottom-nav-item
                    id="add-expense"
                    icon="plus.circle.fill"
                    label="Add Expense"
                    url="/add-expense"
                    :active="true"
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

