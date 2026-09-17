<?php

use Laralite\Application;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Routing\Router;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\View\FileViewFinder;
use Illuminate\Mail\Mailer;
use Symfony\Component\Mailer\Transport;

$basePath = dirname(__DIR__);

// 1. Initialize Laralite Application Singleton
$container = Application::getInstance();
if (! $container instanceof Application) {
    $container = new Application();
    Application::setInstance($container);
}
$container->instance('app', $container);

// 2. Load Environment Variables via phpdotenv
if (file_exists($basePath . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable($basePath);
    $dotenv->safeLoad();
}

// 3. Register Event Dispatcher
$events = new Dispatcher($container);
$container->instance('events', $events);

// 4. Register Router (Module 1: Routing & Controllers)
$router = new Router($events, $container);
$container->instance('router', $router);
$container->instance(Router::class, $router);

// 5. Register Eloquent ORM via Capsule (Module 2: Database)
$capsule = new Capsule($container);
$dbConfig = require $basePath . '/config/database.php';
$defaultConn = $dbConfig['default'];
$capsule->addConnection($dbConfig['connections'][$defaultConn]);
$capsule->setAsGlobal();
$capsule->bootEloquent();
$container->instance('db', $capsule->getDatabaseManager());
$container->instance(Capsule::class, $capsule);

// 6. Register Blade Template Engine (Module 3: Blade Views)
$viewConfig = require $basePath . '/config/view.php';
$files = new Filesystem();
$container->instance('files', $files);

if (! $files->exists($viewConfig['compiled'])) {
    $files->makeDirectory($viewConfig['compiled'], 0755, true);
}

$bladeCompiler = new BladeCompiler($files, $viewConfig['compiled']);
$engineResolver = new EngineResolver();
$engineResolver->register('blade', function () use ($bladeCompiler) {
    return new CompilerEngine($bladeCompiler);
});
$engineResolver->register('php', function () use ($files) {
    return new PhpEngine($files);
});

$finder = new FileViewFinder($files, $viewConfig['paths']);
$viewFactory = new ViewFactory($engineResolver, $finder, $events);
$container->instance('view', $viewFactory);
$container->instance(ViewFactory::class, $viewFactory);

// 7. Register Email Service (Module 4: Mail)
$mailConfig = require $basePath . '/config/mail.php';
$container->singleton('mailer', function ($app) use ($mailConfig, $viewFactory, $events) {
    $driver = $mailConfig['default'] ?? 'smtp';
    $config = $mailConfig['mailers'][$driver] ?? [];

    if ($driver === 'smtp') {
        $scheme = ($config['encryption'] === 'tls') ? 'smtps' : 'smtp';
        $user = rawurlencode($config['username'] ?? '');
        $pass = rawurlencode($config['password'] ?? '');
        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? 1025;
        
        $dsn = ($user || $pass) 
            ? "{$scheme}://{$user}:{$pass}@{$host}:{$port}"
            : "{$scheme}://{$host}:{$port}";

        $transport = Transport::fromDsn($dsn);
    } else {
        $transport = new \Symfony\Component\Mailer\Transport\NullTransport();
    }

    $mailer = new Mailer(
        'default',
        $viewFactory,
        $transport,
        $events
    );

    if (isset($mailConfig['from'])) {
        $mailer->alwaysFrom($mailConfig['from']['address'], $mailConfig['from']['name']);
    }

    return $mailer;
});

/**
 * Handle an incoming HTTP request through middleware pipeline and router.
 *
 * @param \Illuminate\Http\Request $request
 * @param array $middleware
 * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
 */
function handleRequest(Request $request, array $middleware = []) {
    $container = Application::getInstance();
    $router = $container->make('router');

    return (new Pipeline($container))
        ->send($request)
        ->through($middleware)
        ->then(function ($req) use ($router) {
            return $router->dispatch($req);
        });
}

return $container;
