<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to {{ $appName }}</title>
</head>
<body style="font-family: sans-serif; line-height: 1.5; color: #333;">
    <h2>Hello {{ $user->name }},</h2>
    <p>Welcome to <strong>{{ $appName }}</strong>! We are thrilled to have you on board.</p>
    <p>Your registered email is: <code>{{ $user->email }}</code></p>
    <hr>
    <p style="font-size: 0.85em; color: #777;">Sent via Laralite standalone illuminate/mail service.</p>
</body>
</html>
