<?php

namespace App\Http\Controllers;

use App\Models\{Lesson, Subject, SubjectModule, Year};
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LessonController extends Controller
{
    public function index()
    {
        $search = request('search');
        $yearId = request('year_id');
        $subjectId = request('subject_id');
        $moduleId = request('module_id');

        $lessons = Lesson::query()
            ->where('year_id', auth()->user()->year_id)
            ->with(['module', 'subject', 'year'])
            ->when($search, fn($query) => $query->where('name', 'LIKE', "%{$search}%"))
            ->when($yearId, fn($query) => $query->where('year_id', $yearId))
            ->when($subjectId, fn($query) => $query->where('subject_id', $subjectId))
            ->when($moduleId, fn($query) => $query->where('module_id', $moduleId))
            ->orderBy('created_at', 'ASC')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Lessons/Index', [
            'lessons' => $lessons,
            'filters' => request()->only(['search', 'year_id', 'subject_id', 'module_id']),
            'years' => Year::all(),
            'subjects' => Subject::all(),
            'modules' => SubjectModule::all(),
        ]);
    }
    public function create()
    {
        $years = Year::all(); // Fetch all years

        return Inertia::render('Lessons/Create', [
            'years' => $years,
            'subjects' => [],
            'modules' => [],
        ]);
    }

    public function getModules(Request $request, $subjectId)
    {
        $modules = SubjectModule::where('subject_id',  $subjectId)->get();

        return response()->json($modules);
    }

    public function getSubjects(Request $request, $yearId)
    {
        $subjects = Subject::where('year_id', $yearId)->get();

        return response()->json($subjects);
    }
    public function store(Request $request)
    {
        $request->validate([
            'year_id' => 'required',
            'subject_id' => 'required',
            'module_id' => 'required',
            'name' => 'required|string|max:255',
            'datax' => 'required|array',
            'datax.*.content' => 'required|string',
            'activity' => 'required|array',
            'activity.*.title' => 'required|string',
            'activity.*.question' => 'required|string',
            'activity.*.correct_answer' => 'required|string',
        ]);

        $lesson = Lesson::create([
            'year_id' => $request->year_id,
            'subject_id' => $request->subject_id,
            'module_id' => $request->module_id,
            'name' => $request->name,
            'data' => $request->datax,
            'activity' => $request->activity,
        ]);
        dd($lesson);
    }

    public function edit($lessonId)
    {
        $years = Year::all(); // Fetch all years
        return Inertia::render('Lessons/Edit', [
            'lesson' =>  Lesson::find($lessonId),
            'years' => $years,
            'subjects' => [],
            'modules' => []
        ]);
    }

    public function update(Request $request,  $lessonId)
    {
        $request->validate([
            'year_id' => 'required',
            'subject_id' => 'required',
            'module_id' => 'required',
            'name' => 'required|string|max:255',
            'datax' => 'required|array',
            'datax.*.content' => 'required|string',
            'activity' => 'required|array',
            'activity.*.title' => 'required|string',
            'activity.*.question' => 'required|string',
            'activity.*.correct_answer' => 'required|string',
        ]);

        $lesson = Lesson::find($lessonId);
        $lesson->update([
            'year_id' => $request->year_id,
            'subject_id' => $request->subject_id,
            'module_id' => $request->module_id,
            'name' => $request->name,
            'data' => $request->datax,
            'activity' => $request->activity,
        ]);
    }

    public function destroy($lessonId)
    {
        Lesson::find($lessonId)->delete();
        return redirect()->back();
    }
}
