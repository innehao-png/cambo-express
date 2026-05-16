<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompressResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (app()->environment('testing') || !extension_loaded('zlib')) {
            return $response;
        }

        if ($request->header('Accept-Encoding') && str_contains($request->header('Accept-Encoding'), 'gzip')) {
            $content = $response->getContent();
            $compressedContent = gzencode($content, 9);

            $response->setContent($compressedContent);
            $response->headers->set('Content-Encoding', 'gzip');
            $response->headers->set('Content-Length', strlen($compressedContent));
        }

        return $response;
    }
}
