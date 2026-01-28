<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ?
                    $request->user()->load('profile')->toArray() + [
                        'roles' => $request->user()->getRoleNames(),
                        'permissions' => $request->user()->getAllPermissions()->pluck('name'),
                        'is_pro' => $request->user()->is_pro,
                    ] : null,
            ],
            'ziggy' => fn() => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'flash' => [
                'message' => fn() => $request->session()->get('message'),
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
            ],
            'locale' => app()->getLocale(),
            'translations' => function () {
                $locale = app()->getLocale();
                $path = base_path("lang/{$locale}.json");
                return file_exists($path) ? json_decode(file_get_contents($path), true) : [];
            },
            'modules' => function () {
                $settings = \App\Models\Setting::whereIn('key', ['module_pool', 'module_academy', 'module_agenda', 'module_assistant'])
                    ->pluck('value', 'key');

                return [
                    'pool' => ($settings['module_pool'] ?? '1') !== '0' && ($settings['module_pool'] ?? 'true') !== 'false',
                    'academy' => ($settings['module_academy'] ?? '1') !== '0' && ($settings['module_academy'] ?? 'true') !== 'false',
                    'agenda' => ($settings['module_agenda'] ?? '1') !== '0' && ($settings['module_agenda'] ?? 'true') !== 'false',
                    'assistant' => ($settings['module_assistant'] ?? '1') !== '0' && ($settings['module_assistant'] ?? 'true') !== 'false',
                ];
            },
        ];
    }
}
