<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [\App\Http\Controllers\WelcomeController::class, 'index']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function (App\Services\FeedService $feedService) {
        return Inertia::render('Dashboard', [
            'posts' => $feedService->getGlobalFeed(),
            'clubs' => \App\Models\Club::select('id', 'name')->get(),
            'cities' => \App\Models\City::select('id', 'name')->get(),
            'djs' => \App\Models\User::role('DJ')->select('id', 'name')->get(),
        ]);
    })->name('dashboard');

    Route::post('/feed', [\App\Http\Controllers\FeedController::class, 'store'])->name('feed.store');
    Route::post('/feed/{post}/like', [\App\Http\Controllers\FeedController::class, 'like'])->name('feed.like');
    Route::post('/feed/{post}/comment', [\App\Http\Controllers\FeedController::class, 'comment'])->name('feed.comment');
    Route::delete('/feed/posts/{post}', [\App\Http\Controllers\FeedController::class, 'destroy'])->name('feed.destroy');
    Route::delete('/feed/comments/{comment}', [\App\Http\Controllers\FeedController::class, 'destroyComment'])->name('feed.comments.destroy');

    Route::get('/pool', function (App\Services\PoolService $poolService) {
        // Check PRO Access
        $poolIsPro = \App\Models\Setting::where('key', 'pool_is_pro')->value('value') === '1';
        if ($poolIsPro && !auth()->user()->is_pro) {
            return redirect()->route('subscription.index')->with('message', 'The Music Pool is for PRO members only.');
        }

        return Inertia::render('Pool/Index', [
            'songs' => $poolService->searchSongs(request()->all())
        ]);
    })->name('pool');

    Route::get('/academy', function (App\Services\AcademyService $academyService) {
        $academyIsPro = \App\Models\Setting::where('key', 'academy_is_pro')->value('value') === '1';
        if ($academyIsPro && !auth()->user()->is_pro) {
            return redirect()->route('subscription.index')->with('message', 'The Academy is for PRO members only.');
        }

        return Inertia::render('Academy/Index', [
            'courses' => $academyService->getAllCourses()
        ]);
    })->name('academy');

    Route::get('/academy/{course}', function ($courseId, App\Services\AcademyService $academyService) {
        $academyIsPro = \App\Models\Setting::where('key', 'academy_is_pro')->value('value') === '1';
        $course = \App\Models\Course::findOrFail($courseId);

        if (!auth()->user()->is_pro) {
            if ($academyIsPro || $course->is_pro) {
                return redirect()->route('subscription.index')->with('message', 'This course is for PRO members only.');
            }
        }

        return Inertia::render('Academy/Show', [
            'course' => $academyService->getCourseDetails($courseId)
        ]);
    })->name('academy.show');

    Route::get('/academy/exam/{exam}', function ($examId) {
        return Inertia::render('Academy/Exam', [
            'exam' => \App\Models\Exam::with('questions.answers')->findOrFail($examId)
        ]);
    })->name('academy.exam');

    Route::post('/academy/exam/{exam}/submit', function ($examId, \Illuminate\Http\Request $request, App\Services\AcademyService $academyService) {
        $result = $academyService->submitExam($request->user(), $examId, $request->input('answers', []));

        // Return to course or show result
        // For now, redirect back with flash message
        return redirect()->route('academy.show', $result->exam->chapter->course_id)
            ->with('success', $result->passed ? 'You passed the exam!' : 'You failed. Try again.');
    })->name('academy.submit');

    Route::post('/academy/chapters/{chapter}/complete', function (\App\Models\Chapter $chapter, \Illuminate\Http\Request $request, App\Services\AcademyService $academyService) {
        $academyService->markChapterComplete($request->user(), $chapter->id);
        return back(); // Inertia will reload props
    })->name('academy.chapters.complete');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // DJ Equipment
    Route::resource('dj-equipment', \App\Http\Controllers\DjEquipmentController::class)->only(['store', 'update', 'destroy']);

    // AI Assistant
    Route::get('/assistant', [\App\Http\Controllers\AssistantController::class, 'index'])->name('assistant.index');
    Route::get('/assistant/{model}', [\App\Http\Controllers\AssistantController::class, 'show'])->name('assistant.chat');
    Route::post('/assistant/{model}/chat', [\App\Http\Controllers\AssistantController::class, 'chat'])->name('assistant.sendMessage');

    // PRO Subscription
    Route::get('/subscription', [\App\Http\Controllers\SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('/subscription/subscribe', [\App\Http\Controllers\SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
    Route::get('/subscription', [\App\Http\Controllers\SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('/subscription/subscribe', [\App\Http\Controllers\SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
    Route::post('/subscription/trial', [\App\Http\Controllers\SubscriptionController::class, 'activateTrial'])->name('subscription.activateTrial');
    Route::get('/subscription/success', [\App\Http\Controllers\SubscriptionController::class, 'success'])->name('subscription.success');
});

// Flow Redirects (Public to handle Session Loss)
Route::match(['get', 'post'], '/subscription/return', [\App\Http\Controllers\SubscriptionController::class, 'return'])->name('subscription.return');

Route::get('/profile/{username}', [ProfileController::class, 'show'])->name('profile.show');

Route::middleware(['auth', 'verified', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'users_count' => \App\Models\User::count(),
                'songs_count' => \App\Models\Song::count(),
                'courses_count' => \App\Models\Course::count(),
            ]
        ]);
    })->name('dashboard');

    // Music Manager
    Route::resource('music', \App\Http\Controllers\Admin\MusicController::class)->parameters([
        'music' => 'song'
    ]);

    // Academy Manager
    Route::resource('academy', \App\Http\Controllers\Admin\AcademyController::class)->parameters([
        'academy' => 'course',
    ]);
    Route::post('/academy/{course}/chapters', [\App\Http\Controllers\Admin\AcademyController::class, 'storeChapter'])->name('academy.chapters.store');
    Route::post('/academy/{course}/toggle', [\App\Http\Controllers\Admin\AcademyController::class, 'toggleActive'])->name('academy.toggle');
    Route::post('/academy/{course}/toggle-pro', [\App\Http\Controllers\Admin\AcademyController::class, 'togglePro'])->name('academy.togglePro');
    Route::delete('/chapters/{chapter}', [\App\Http\Controllers\Admin\AcademyController::class, 'destroyChapter'])->name('chapters.destroy');

    // Global Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');

    // User Manager
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::post('/users/{user}/subscription', [\App\Http\Controllers\Admin\UserController::class, 'manageSubscription'])->name('users.subscription');

    // Social Platforms
    Route::resource('social-platforms', \App\Http\Controllers\Admin\SocialPlatformController::class);

    // Taxonomy
    Route::get('taxonomy', [\App\Http\Controllers\Admin\TaxonomyController::class, 'index'])->name('taxonomy.index');
    Route::post('countries', [\App\Http\Controllers\Admin\CountryController::class, 'store'])->name('countries.store');
    Route::delete('countries/{country}', [\App\Http\Controllers\Admin\CountryController::class, 'destroy'])->name('countries.destroy');
    Route::post('countries/{country}/toggle', [\App\Http\Controllers\Admin\TaxonomyController::class, 'toggleCountry'])->name('countries.toggle');

    Route::post('cities', [\App\Http\Controllers\Admin\CityController::class, 'store'])->name('cities.store');
    Route::delete('cities/{city}', [\App\Http\Controllers\Admin\CityController::class, 'destroy'])->name('cities.destroy');
    Route::post('cities/{city}/toggle', [\App\Http\Controllers\Admin\TaxonomyController::class, 'toggleCity'])->name('cities.toggle');

    Route::post('clubs', [\App\Http\Controllers\Admin\ClubController::class, 'store'])->name('clubs.store');
    Route::delete('clubs/{club}', [\App\Http\Controllers\Admin\ClubController::class, 'destroy'])->name('clubs.destroy');

    Route::post('dj-types', [\App\Http\Controllers\Admin\DjTypeController::class, 'store'])->name('dj-types.store');
    Route::delete('dj-types/{djType}', [\App\Http\Controllers\Admin\DjTypeController::class, 'destroy'])->name('dj-types.destroy');

    // Equipment Manager
    Route::get('equipment', [\App\Http\Controllers\Admin\EquipmentController::class, 'index'])->name('equipment.index');
    Route::post('brands', [\App\Http\Controllers\Admin\EquipmentController::class, 'storeBrand'])->name('brands.store');
    Route::patch('brands/{brand}', [\App\Http\Controllers\Admin\EquipmentController::class, 'updateBrand'])->name('brands.update');
    Route::delete('brands/{brand}', [\App\Http\Controllers\Admin\EquipmentController::class, 'destroyBrand'])->name('brands.destroy');

    Route::post('equipment-types', [\App\Http\Controllers\Admin\EquipmentController::class, 'storeType'])->name('equipment-types.store');
    Route::patch('equipment-types/{type}', [\App\Http\Controllers\Admin\EquipmentController::class, 'updateType'])->name('equipment-types.update');
    Route::delete('equipment-types/{type}', [\App\Http\Controllers\Admin\EquipmentController::class, 'destroyType'])->name('equipment-types.destroy');

    Route::post('equipment-models', [\App\Http\Controllers\Admin\EquipmentController::class, 'storeModel'])->name('equipment-models.store');
    Route::patch('equipment-models/{model}', [\App\Http\Controllers\Admin\EquipmentController::class, 'updateModel'])->name('equipment-models.update');
    Route::delete('equipment-models/{model}', [\App\Http\Controllers\Admin\EquipmentController::class, 'destroyModel'])->name('equipment-models.destroy');
});

require __DIR__ . '/auth.php';

// Flow Webhook (Server-to-Server, Public)
Route::post('/subscription/confirm', [\App\Http\Controllers\SubscriptionController::class, 'confirm'])->name('subscription.confirm');
