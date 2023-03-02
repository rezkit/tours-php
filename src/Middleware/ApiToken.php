<?php

namespace RezKit\Tours\Middleware;

use Closure;
use Psr\Http\Message\RequestInterface;

/**
 * Middleware generator for API token authentication
 */
abstract class ApiToken {
    public static function create(string $apiKey): Closure {
        return function(callable $handler) use ($apiKey) {
            return function (RequestInterface $request, array $options) use ($handler, $apiKey) {
                $request = $request->withHeader('Authorization', 'Bearer ' . $apiKey);
                return $handler($request, $options);
            };
        };
    }
}

