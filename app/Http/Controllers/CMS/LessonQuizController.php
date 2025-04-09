<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LessonQuizController extends Controller
{
    // View all quizzes for a specific lesson
    public function index(Lesson $lesson)
    {
        $quizzes = $lesson->quizzes()->paginate(5); // You can change 5 to any per-page value

        return Inertia::render('LessonQuizzes/View', [
            'lesson' => $lesson,
            'quizzes' => $quizzes,
        ]);
    }

    // Show form to create a quiz
    public function create(Request $request, $lessonId)
    {
        return Inertia::render('LessonQuizzes/Create', [
            'lesson' => Lesson::where('id', $lessonId)->first(),
        ]);
    }

    // Store the new quiz
    public function store(Request $request): void
    {
        $validated = $request->validate([
            'question'  => 'required|string|max:255',
            'answer1'   => 'required|string|max:255',
            'answer2'   => 'required|string|max:255',
            'answer3'   => 'required|string|max:255',
            'answer4'   => 'required|string|max:255',
            'correct'   => 'required|in:answer1,answer2,answer3,answer4',
            'lesson_id' => 'required|exists:module_lessons,id',
        ]);

        $lesson = Lesson::where('id', $request->lesson_id)->first();
        $lesson->quizzes()->create($validated);
    }

    // Show form to edit a quiz
    public function edit(Request $request, $quizId)
    {
        $quiz = Quiz::where('id', $quizId)->first();
        $lesson = $quiz->lesson; // assuming you have relationship set up
        return Inertia::render('LessonQuizzes/Edit', [
            'quiz' => $quiz,
            'lesson' => $lesson,
        ]);
    }

    // Update the quiz
    public function update(Request $request, $quizId)
    {
        // return $request->all();

        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer1' => 'required|string|max:255',
            'answer2' => 'required|string|max:255',
            'answer3' => 'required|string|max:255',
            'answer4' => 'required|string|max:255',
            'correct'  => 'required|in:answer1,answer2,answer3,answer4',
            'lesson_id' => 'required|exists:module_lessons,id'
        ]);

        $quiz = Quiz::where('id', $quizId)->first();
        $quiz->update($validated);
    }

    // Delete the quiz
    public function destroy($quizId)
    {
        $quiz = Quiz::where('id', $quizId)->first();
        $quiz->delete();
    }
}
