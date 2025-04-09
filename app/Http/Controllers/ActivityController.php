<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    public function index(Request $request): Response
    {
        // dd("Test");
        $lesson = Lesson::with('activities.questions')->find($request->lesson);

        return Inertia::render('Activities/Index', compact('lesson'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'step' => 'required|integer|min:1',
        ]);

        $activity = Activity::create($validated);

        return redirect()->back()->with('activity', $activity);
    }

    // Add question to an existing activity
    public function addQuestion(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
        ]);

        // Create a new question under this activity
        $activity->questions()->create($validated);

        return redirect()->back()->with('success', 'Question added successfully!');
    }

}
