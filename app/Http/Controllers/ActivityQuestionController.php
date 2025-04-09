<?php

namespace App\Http\Controllers;

use App\Models\ActivityQuestion;
use Illuminate\Http\Request;

class ActivityQuestionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'question' => 'required|string',
            'answer' => 'required|in:true,false',
        ]);

        $question = ActivityQuestion::create($validated);

        return redirect()->back()->with('question', $question);
    }
}