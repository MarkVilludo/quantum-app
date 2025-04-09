<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QuizController extends Controller {
    public function index(Lesson $lesson): Response {

        return Inertia::render('Quiz/Index', [
            'lesson' => $lesson->load('quizzes'),
        ]);
    }

    public function store(Request $request, Lesson $lesson) {
        $request->validate([
            'question' => 'required|string',
            'answer1' => 'required|string',
            'answer2' => 'required|string',
            'answer3' => 'required|string',
            'answer4' => 'required|string',
            'correct' => 'required|string',
        ]);

        Quiz::create([
            'lesson_id' => $lesson->id, // Ensuring lesson_id is stored
            'question' => $request->question,
            'answer1' => $request->answer1,
            'answer2' => $request->answer2,
            'answer3' => $request->answer3,
            'answer4' => $request->answer4,
            'correct' => $request->correct,
        ]);

        return redirect()->back();
    }
}
