<?php

namespace App\Http\Middleware;

use Closure;

class SecureHeaders
{

    private $unwantedHeaderList = [
        'X-Powered-By',
        'Server',
    ];
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Strict-Transport-Security', true);
        $response->headers->set('X-Frame-Options', 'deny');
        $response->headers->set('Access-Control-Allow-Methods', 'GET,HEAD,POST,PUT,DELETE');
        $this->removeUnwantedHeaders($this->unwantedHeaderList);
        return $response;
    }

    private function removeUnwantedHeaders($headerList)
    {
        foreach ($headerList as $header)
            header_remove($header);
    }
}
// Enumerate unwanted headers

/**
 * @param $headerList
 */
