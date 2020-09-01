<?php

use Websoft\Config;
use Websoft\Response;


if (! function_exists('autoload_app_classes')) {
    /**
     * Autoloading defined app classes from composer autoload psr-4
     *
     */
    function autoload_app_classes($dir)
    {
        $composer = json_decode(file_get_contents("$dir/composer.json"), 1);
        $namespaces = $composer['autoload']['psr-4'];

        // Foreach namespace specified in the composer, load the given classes
        foreach ($namespaces as $namespace => $classPaths) {
            if (!is_array($classPaths)) {
                $classPaths = array($classPaths);
            }
            spl_autoload_register(function ($className) use ($namespace, $classPaths, $dir) {
                // Check if the namespace matches the class we are looking for
                if (preg_match("#^".preg_quote($namespace)."#", $className)) {
                    // Remove the namespace from the file path since it's psr4
                    $className = str_replace($namespace, "", $className);
                    $fileName = preg_replace("#\\\\#", "/", $className).".php";
                    foreach ($classPaths as $classPath) {
                        $fullPath = $dir."/".$classPath."/$fileName";
                        if (file_exists($fullPath)) {
                            include_once $fullPath;
                        }
                    }
                }
            });
        }
    }
}

if (! function_exists('response')) {
    /**
     * Return a new response from the application.
     *
     */
    function response()
    {
        $response = new Response();

        return $response;
    }
}

if (! function_exists('get_config')) {
    /**
     * Gets the value of an config variable.
     *
     * @param string $key 'app.name'
     * @param mixed  $default
     * @return string|array
     */
    function get_config($key, $default = null)
    {
        $config = new Config;

        return $config->read($key, $default);
    }
}

if (! function_exists('blank')) {
    /**
     * Determine if the given value is "blank".
     *
     * @param  mixed  $value
     * @return bool
     */
    function blank($value)
    {
        if (is_null($value)) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        if (is_numeric($value) || is_bool($value)) {
            return false;
        }

        if ($value instanceof Countable) {
            return count($value) === 0;
        }

        return empty($value);
    }
}

if (! function_exists('filled')) {
    /**
     * Determine if a value is "filled".
     *
     * @param  mixed  $value
     * @return bool
     */
    function filled($value)
    {
        return ! blank($value);
    }
}

if (! function_exists('transform')) {
    /**
     * Transform the given value if it is present.
     *
     * @param  mixed  $value
     * @param  callable  $callback
     * @param  mixed  $default
     * @return mixed|null
     * 
     * $result = transform(5, function ($value) {
     *   return $value * 2;
     * });
     * 
     */
    function transform($value, callable $callback, $default = null)
    {
        if (filled($value)) {
            return $callback($value);
        }

        if (is_callable($default)) {
            return $default($value);
        }

        return $default;
    }
}
