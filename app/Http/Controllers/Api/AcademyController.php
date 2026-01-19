<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AcademyService;
use Illuminate\Http\Request;

class AcademyController extends Controller
{
    protected $academyService;

    public function __construct(AcademyService $academyService)
    {
        $this->academyService = $academyService;
    }

    public function index()
    {
        return $this->academyService->getAllCourses();
    }

    public function show($id)
    {
        return $this->academyService->getCourseDetails($id);
    }

    public function submitExam(Request $request, $examId)
    {
        $validated = $request->validate([
            'answers' => ['required', 'array'],
            // Validating that keys are question IDs and values are answer IDs/text
        ]);

        $result = $this->academyService->submitExam($request->user(), $examId, $validated['answers']);

        return response()->json([
            'message' => $result->passed ? 'Congratulations! You passed.' : 'You did not pass. Try again.',
            'result' => $result
        ]);
    }
}
