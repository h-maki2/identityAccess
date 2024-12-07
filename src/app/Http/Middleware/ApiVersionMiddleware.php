<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiVersionMiddleware
{
    private const API_DEFAULT_VERSION = 'v1';

    public function handle(Request $request, Closure $next): Response
    {
        $acceptHeader = $request->header('Accept');
        $request->attributes->set('api_version', $this->apiVersion($acceptHeader));
        return $next($request);
    }

    private function apiVersion(string $acceptHeader): string
    {
        if (preg_match('/application\/vnd\.example\.(v\d+)\+json/', $acceptHeader, $matches)) {
            return $matches[1];
        }

        return self::API_DEFAULT_VERSION;
    }
}
