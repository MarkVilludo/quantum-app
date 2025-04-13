<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Year;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::with('year');

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return Inertia::render('Subjects/Index', [
            'subjects' => $query->latest()->orderBy('year_id', 'asc')->paginate(10)->withQueryString(),
            'years' => Year::all(),
            'filters' => $request->only('search'),
        ]);
    }


    public function create()
    {
        // Fetch all years to show in the dropdown
        $years = Year::all();
        return inertia('Subjects/Create', ['years' => $years]);
    }

    /**
     * Store a newly created subject.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name',
            'year_id' => 'required|exists:years,id',
        ]);

        // Create subject
        Subject::create($validated);
    }


    public function edit(Subject $subject)
    {
        $years = Year::all();

        // Pass the subject and years data to the Inertia view
        return Inertia::render('Subjects/Edit', [
            'subject' => $subject,
            'years' => $years
        ]);
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year_id' => 'required|exists:years,id',
        ]);

        $subject->update($validated);
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->route('subjects.index')->with('success', 'Subject deleted successfully.');
    }
}
