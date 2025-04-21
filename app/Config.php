<?php
declare(strict_types=1);

namespace App;

/*
 * why use an environment variables wrapper?
 * default values and error handling.
 * decoupling from superglobals,
 * hence, extensible code
 * that allows loading environment variables from somewhere else without breaking the application
 */
class Config
{
    public function __construct(
        private readonly array $config, // the class receives an array
    ){
    }

    public function get(string $name, mixed $default = null): mixed {
        // split the name into an array using the '.' as a delimiter
        $path = explode('.', $name);

        // check for the first key in the path
        $value = $this->config[array_shift($path)] ?? null;

        if($value === null) {
            return $default;
        }

        // for each key in the path array
        foreach($path as $key) {
            // if key doesn't exist return the default value
            if(! isset($value[$key])) {
                return $default;
            }

            // if the key exists, then the current value if replaced with the new found value
            // either an inner array of the current array, or a real value inside the current array
            $value = $value[$key];
        }

        return $value;
    }
}