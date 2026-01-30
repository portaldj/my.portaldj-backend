<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = 'en';

        if ($request->session()->has('locale')) {
            $locale = $request->session()->get('locale');
        } elseif (auth()->check()) {
            $locale = auth()->user()->locale ?? 'en';
        } elseif ($request->header('Accept-Language')) {
            $locale = substr($request->header('Accept-Language'), 0, 2);
            if (!in_array($locale, ['en', 'es'])) {
                $locale = 'en';
            }
        }

        if (!in_array($locale, ['en', 'es'])) {
            $locale = 'en';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
