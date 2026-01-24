<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageOptimizationService;

class AcademyController extends Controller
{
    protected $imageService;

    public function __construct(ImageOptimizationService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index()
    {
        return Inertia::render('Admin/Academy/Index', [
            'courses' => Course::withCount('chapters')->latest()->paginate(10)
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Academy/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail' => 'nullable|image|max:10240', // 10MB
            'is_pro' => 'boolean',
        ]);

        if ($request->hasFile('thumbnail')) {
            // Resize: thumb (400x225), optimized (1280x720)
            $variants = [
                'thumb' => [400, 225],
                'optimized' => [1280, 720]
            ];
            $images = $this->imageService->handle($request->file('thumbnail'), 'thumbnails', $variants);
            $validated['thumbnail_path'] = $images['original']; // Store original base path
        }

        $course = Course::create($validated);

        return redirect()->route('admin.academy.show', $course->id)->with('success', 'Course created. Now add chapters.');
    }

    public function edit(Course $course)
    {
        return Inertia::render('Admin/Academy/Edit', [
            'course' => $course
        ]);
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail' => 'nullable|image|max:10240',
            'is_pro' => 'boolean',
        ]);

        if ($request->hasFile('thumbnail')) {
            // Delete old
            if ($course->thumbnail_path) {
                // Try delete variants based on old path being 'optimized' or 'original'
                // Since this is a new system, old paths might just be files.
                // The delete() method in service blindly tries variants.
                // If the old path was 'path/hash_optimized.jpg', the directory/filename parsing might be tricky if not consistent.
                // But for new uploads it will work. For legacy files, it just deletes the file.
                // However, we should pass the keys we expect.
                $this->imageService->delete($course->thumbnail_path, ['thumb', 'optimized']);
            }

            $variants = [
                'thumb' => [400, 225],
                'optimized' => [1280, 720]
            ];
            $images = $this->imageService->handle($request->file('thumbnail'), 'thumbnails', $variants);
            $validated['thumbnail_path'] = $images['original'];
        }

        $course->update($validated);

        return redirect()->route('admin.academy.index')->with('success', 'Course updated successfully.');
    }

    public function show(Course $course)
    {
        return Inertia::render('Admin/Academy/Show', [
            'course' => $course->load([
                'chapters' => function ($q) {
                    $q->orderBy('order');
                }
            ]),
        ]);
    }

    public function storeChapter(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'video_url' => 'nullable|url',
            'content' => 'nullable|string',
            'order' => 'integer',
        ]);

        $course->chapters()->create($validated);

        return redirect()->back()->with('success', 'Chapter added.');
    }


    public function destroyChapter(\App\Models\Chapter $chapter)
    {
        $chapter->delete();
        return redirect()->back()->with('success', 'Chapter deleted.');
    }

    public function destroy($id)
    {
        $course = Course::with('chapters.exams')->findOrFail($id);

        // Delete thumbnail if exists
        if ($course->thumbnail_path) {
            $this->imageService->delete($course->thumbnail_path, ['thumb', 'optimized']);
        }

        // Manually delete children to ensuring everything cleans up even if cascade fails
        foreach ($course->chapters as $chapter) {
            $chapter->exams()->delete(); // Exams
            $chapter->delete(); // Chapter
        }

        $course->delete();

        return redirect()->route('admin.academy.index')->with('success', 'Course deleted successfully.');
    }

    public function toggleActive(Course $course)
    {
        $course->update(['is_active' => !$course->is_active]);

        $status = $course->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Course {$status}.");
    }

    public function togglePro(Course $course)
    {
        $course->update(['is_pro' => !$course->is_pro]);
        return back();
    }
}
