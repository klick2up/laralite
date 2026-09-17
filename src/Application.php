<?php

namespace Laralite;

use Illuminate\Container\Container;

class Application extends Container
{
    /**
     * Determine if the application is running unit tests.
     *
     * @return bool
     */
    public function runningUnitTests()
    {
        return false;
    }

    /**
     * Get or check the current application environment.
     *
     * @param  string|array  ...$environments
     * @return string|bool
     */
    public function environment(...$environments)
    {
        $env = env('APP_ENV', 'local');

        if (count($environments) === 0) {
            return $env;
        }

        return in_array($env, is_array($environments[0]) ? $environments[0] : $environments);
    }

    /**
     * Determine if application is in local environment.
     *
     * @return bool
     */
    public function isLocal()
    {
        return $this->environment('local');
    }

    /**
     * Get the base path of the Laralite installation.
     *
     * @param  string  $path
     * @return string
     */
    public function basePath($path = '')
    {
        return dirname(__DIR__) . ($path ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : '');
    }
}
