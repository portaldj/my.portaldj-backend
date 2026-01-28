<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        // Default to enabled if setting not found
        $value = \App\Models\Setting::where('key', "module_{$module}")->value('value');

        if ($value === '0' || $value === 'false') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Module disabled.'], 403);
            }
            return redirect()->route('dashboard')->with('error', __("This module is currently disabled."));
        }

        return $next($request);
    }
}
