<?php

define('LARALITE_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

// Bootstrap framework components
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Load route definitions
require_once __DIR__ . '/../routes/web.php';

use Illuminate\Http\Request;

// Capture incoming HTTP Request from PHP globals
$request = Request::capture();

// Dispatch request through pipeline & router
$response = handleRequest($request);

// Send response to client
$response->send();
