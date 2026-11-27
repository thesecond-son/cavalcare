<?php

namespace App\Http\Middleware\AdminOs;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security headers for every Caval OS page, including the sign-in screens.
 *
 * Framing: nginx already sends "X-Frame-Options: SAMEORIGIN" on every page.
 * A second, different X-Frame-Options value makes browsers distrust both, so
 * the app does not send one. Instead it sends CSP frame-ancestors 'none',
 * which modern browsers obey over X-Frame-Options: no site, not even this
 * one, may show Caval OS inside a frame.
 */
class OsSecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('Content-Security-Policy', "frame-ancestors 'none'");
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        $response->headers->set('Referrer-Policy', 'same-origin');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        // Admin pages must never be cached by the browser or a proxy.
        $response->headers->set('Cache-Control', 'no-store, private');

        return $response;
    }
}
