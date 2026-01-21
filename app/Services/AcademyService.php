<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Exam;
use App\Models\User;
use App\Models\ExamResult;
use App\Models\Question;

class AcademyService
{
    public function getAllCourses()
    {
        $userId = auth()->id();

        return Course::where('is_active', true)
            ->withCount('chapters')
            ->withCount([
                'chapters as user_completed_chapters_count' => function ($query) use ($userId) {
                    $query->whereHas('users', function ($q) use ($userId) {
                        $q->where('user_id', $userId);
                    });
                }
            ])
            ->get()
            ->map(function ($course) {
                $course->progress = $course->chapters_count > 0
                    ? round(($course->user_completed_chapters_count / $course->chapters_count) * 100)
                    : 0;
                return $course;
            });
    }

    public function getCourseDetails(int $courseId)
    {
        $course = Course::where('is_active', true)
            ->with([
                'chapters' => function ($query) {
                    $query->orderBy('order');
                },
                'chapters.exam'
            ])
            ->findOrFail($courseId);

        // Append completed status to chapters
        $completedChapterIds = auth()->user()->completedChapters()->where('course_id', $courseId)->pluck('chapter_id')->toArray();

        foreach ($course->chapters as $chapter) {
            $chapter->is_completed = in_array($chapter->id, $completedChapterIds);
        }

        return $course;
    }

    public function markChapterComplete(User $user, int $chapterId)
    {
        if (!$user->completedChapters()->where('chapter_id', $chapterId)->exists()) {
            $user->completedChapters()->attach($chapterId, ['completed_at' => now()]);
            return true;
        }
        return false;
    }

    public function submitExam(User $user, int $examId, array $answers)
    {
        $exam = Exam::with(['questions.answers'])->findOrFail($examId);

        $totalScore = 0;
        $maxScore = $exam->questions->sum('points');
        // Simple scoring: 1 point per question roughly, or use 'points' col

        foreach ($exam->questions as $question) {
            $submittedAnswerText = $answers[$question->id] ?? null;
            if (!$submittedAnswerText)
                continue;

            // Find correct answer (assuming text match or ID match if we used IDs)
            // For MVP let's assume multiple choice ID was sent or text match
            // Here assuming we sent Answer ID
            $correctAnswer = $question->answers()->where('is_correct', true)->first();

            if ($correctAnswer && $submittedAnswerText == $correctAnswer->id) {
                $totalScore += $question->points;
            }
        }

        // Determine Pass/Fail
        // Passing score is usually raw int in DB, e.g. 7 (out of 10)
        // If passing_score is percentage, calculate accordingly.
        // Let's assume passing_score is raw points required.
        $passed = $totalScore >= $exam->passing_score;

        return ExamResult::create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'score' => $totalScore,
            'passed' => $passed
        ]);
    }
}
