<?php

require __DIR__ . '/vendor/autoload.php';

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Http\Request;

echo "\n=========================================\n";
echo "   RUNNING LARALITE INTEGRATION TESTS    \n";
echo "=========================================\n\n";

$app = require __DIR__ . '/bootstrap/app.php';
require __DIR__ . '/routes/web.php';

$failed = 0;
$passed = 0;

function assertTest(bool $condition, string $description) {
    global $passed, $failed;
    if ($condition) {
        echo " [PASS] {$description}\n";
        $passed++;
    } else {
        echo " [FAIL] {$description}\n";
        $failed++;
    }
}

// -------------------------------------------------------------
// Module 2 Test: Eloquent ORM & Capsule Setup
// -------------------------------------------------------------
echo "--- Testing Module 2: Eloquent ORM & Capsule Setup ---\n";
try {
    Capsule::schema()->dropIfExists('users');
    Capsule::schema()->create('users', function ($table) {
        $table->increments('id');
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        $table->timestamps();
    });

    $user = User::create([
        'name' => 'Alice Developer',
        'email' => 'alice@laralite.dev',
        'password' => password_hash('secret123', PASSWORD_BCRYPT),
    ]);

    assertTest($user->id > 0, "User model created with ID: {$user->id}");
    
    $fetchedUser = User::where('email', 'alice@laralite.dev')->first();
    assertTest($fetchedUser !== null && $fetchedUser->name === 'Alice Developer', "Eloquent query returned expected record");
} catch (\Throwable $e) {
    assertTest(false, "Module 2 Exception: " . $e->getMessage());
}

// -------------------------------------------------------------
// Module 3 Test: Blade Template Engine Integration
// -------------------------------------------------------------
echo "\n--- Testing Module 3: Blade Template Engine Integration ---\n";
try {
    $response = view('home', [
        'appName' => 'Laralite Framework',
        'users' => User::all(),
        'status' => 'All Systems Operational',
    ]);

    assertTest($response instanceof \Illuminate\Http\Response, "view() helper returns Illuminate Http Response");
    assertTest($response->getStatusCode() === 200, "Response status code is 200 OK");
    $content = $response->getContent();
    assertTest(str_contains($content, 'Welcome to Laralite Framework'), "Blade template compiled app name accurately");
    assertTest(str_contains($content, 'Alice Developer'), "Blade @foreach loop rendered Eloquent model data");
} catch (\Throwable $e) {
    assertTest(false, "Module 3 Exception: " . $e->getMessage());
}

// -------------------------------------------------------------
// Module 1 Test: Routing, Middleware & Controllers
// -------------------------------------------------------------
echo "\n--- Testing Module 1: Routing, Middleware & Controllers ---\n";
try {
    // Test 1: GET / (Root route mapped to [HomeController::class, 'index'] + LoggingMiddleware)
    $request = Request::create('/', 'GET');
    $response = handleRequest($request);
    
    assertTest($response->getStatusCode() === 200, "Router dispatched request to [HomeController::class, 'index'] with 200 status");
    assertTest($response->headers->get('X-Laralite-Framework') === 'Active', "LoggingMiddleware pipeline executed and attached response header");

    // Test 2: GET /api/users (Group route with AuthMiddleware and LoggingMiddleware)
    $apiReq = Request::create('/api/users', 'GET');
    $apiResp = handleRequest($apiReq);
    assertTest($apiResp->getStatusCode() === 200, "Route group /api/users executed successfully");

    // Test 3: Unauthorized Request simulation
    $unauthReq = Request::create('/api/users', 'GET', [], [], [], ['HTTP_AUTHORIZATION' => 'UnauthorizedToken']);
    $unauthResp = handleRequest($unauthReq);
    assertTest($unauthResp->getStatusCode() === 401, "AuthMiddleware intercepted unauthorized request with 401 status");
} catch (\Throwable $e) {
    assertTest(false, "Module 1 Exception: " . $e->getMessage());
}

// -------------------------------------------------------------
// Module 4 Test: Email Service (illuminate/mail)
// -------------------------------------------------------------
echo "\n--- Testing Module 4: Email Service (illuminate/mail) ---\n";
try {
    $user = User::first();
    $mailable = new WelcomeMail($user);
    $renderedHtml = $mailable->render();

    assertTest(str_contains($renderedHtml, 'Welcome to Laralite'), "Mailable rendered HTML subject/content");
    assertTest(str_contains($renderedHtml, 'alice@laralite.dev'), "Mailable rendered dynamic user data via Blade view");
    assertTest(app()->bound('mailer'), "Mailer service correctly bound in Laralite container");
} catch (\Throwable $e) {
    assertTest(false, "Module 4 Exception: " . $e->getMessage());
}

// -------------------------------------------------------------
// Module 5 Test: Custom Artisan Generators CLI
// -------------------------------------------------------------
echo "\n--- Testing Module 5: Custom Artisan Generator CLI ---\n";
try {
    $generatedFile = __DIR__ . '/app/Controllers/TestGenController.php';
    if (file_exists($generatedFile)) {
        unlink($generatedFile);
    }

    $cmd = 'php ' . escapeshellarg(__DIR__ . '/artisan') . ' make:controller TestGenController';
    $output = shell_exec($cmd);

    assertTest(file_exists($generatedFile), "Artisan command make:controller created TestGenController.php");
    
    if (file_exists($generatedFile)) {
        $fileContent = file_get_contents($generatedFile);
        assertTest(str_contains($fileContent, 'class TestGenController extends Controller'), "Stub placeholders {{class}} correctly replaced");
        assertTest(str_contains($fileContent, 'namespace App\Controllers;'), "Stub placeholders {{namespace}} correctly replaced");
        unlink($generatedFile); // cleanup
    }
} catch (\Throwable $e) {
    assertTest(false, "Module 5 Exception: " . $e->getMessage());
}

echo "\n=========================================\n";
echo " TEST RESULTS: {$passed} Passed, {$failed} Failed \n";
echo "=========================================\n\n";

exit($failed === 0 ? 0 : 1);
