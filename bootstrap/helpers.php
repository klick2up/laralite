<?php

use Illuminate\Container\Container;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

if (! function_exists('app')) {
    /**
     * Get the available container instance or resolve a binding.
     *
     * @param  string|null  $abstract
     * @param  array  $parameters
     * @return mixed|\Illuminate\Container\Container
     */
    function app($abstract = null, array $parameters = [])
    {
        $container = Container::getInstance();

        if (is_null($abstract)) {
            return $container;
        }

        return $container->make($abstract, $parameters);
    }
}

if (! function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    function env($key, $default = null)
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
}

if (! function_exists('view')) {
    /**
     * Get the evaluated view contents for the given view or return an Illuminate Http Response.
     *
     * @param  string|null  $view
     * @param  array  $data
     * @param  array  $mergeData
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    function view($view = null, $data = [], $mergeData = [])
    {
        $factory = app('view');

        if (func_num_args() === 0) {
            return $factory;
        }

        $content = $factory->make($view, $data, $mergeData)->render();

        return new Response($content, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }
}

if (! function_exists('response')) {
    /**
     * Return a new response from the application or response helper object.
     *
     * @param  mixed  $content
     * @param  int  $status
     * @param  array  $headers
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|object
     */
    function response($content = null, $status = 200, array $headers = [])
    {
        if (is_null($content)) {
            return new class {
                public function json($data = [], $status = 200, array $headers = [], $options = 0) {
                    return new JsonResponse($data, $status, $headers, $options);
                }
                public function make($content = '', $status = 200, array $headers = []) {
                    return new Response($content, $status, $headers);
                }
            };
        }

        if (is_array($content) || $content instanceof \JsonSerializable) {
            return new JsonResponse($content, $status, $headers);
        }

        return new Response($content, $status, $headers);
    }
}
