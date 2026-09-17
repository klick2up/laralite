<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $appName }} - Micro Framework</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #0f172a; color: #f8fafc; padding: 2rem; }
        .card { background: #1e293b; border-radius: 8px; padding: 1.5rem; margin-bottom: 1rem; border: 1px solid #334155; }
        h1 { color: #38bdf8; margin-top: 0; }
        .badge { background: #0284c7; color: #ffffff; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.875rem; }
        ul { padding-left: 1.25rem; }
        li { margin-bottom: 0.5rem; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Welcome to {{ $appName }}</h1>
        <p>Status: <span class="badge">{{ $status }}</span></p>
    </div>

    <div class="card">
        <h2>Registered Users (Eloquent ORM)</h2>
        @if(count($users) > 0)
            <ul>
                @foreach($users as $user)
                    <li><strong>{{ $user->name }}</strong> ({{ $user->email }})</li>
                @endforeach
            </ul>
        @else
            <p>No users found in SQLite database yet.</p>
        @endif
    </div>
</body>
</html>
