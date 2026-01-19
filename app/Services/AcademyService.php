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
        return Course::where('is_active', true)->withCount('chapters')->get();
    }

    public function getCourseDetails(int $courseId)
    {
        return Course::where('is_active', true)->with(['chapters.exam'])->findOrFail($courseId);
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
