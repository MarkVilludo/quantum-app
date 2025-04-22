<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{SubjectModule, Subject};
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class SubjectModuleController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = SubjectModule::with(['subject' => function ($query) {
            $query->where('year_id', auth()->user()->year_id);
        }]);
        $query->with('subject.year');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        return Inertia::render('SubjectModules/Index', [
            'modules' => $query->latest()->paginate(10),
            'filters' => $request->only('search', 'subject_id'),
            'subjects' => Subject::all(),
        ]);
    }

    public function create()
    {
        return Inertia::render('SubjectModules/Create', [
            'subjects' => Subject::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subject_modules')->where(function ($query) use ($request) {
                    return $query->where('subject_id', $request->input('subject_id'));
                }),
            ],
            'subject_id' => 'required|exists:subjects,id',
        ], [
            'name.required' => 'The module name is required.',
            'name.unique' => 'The module name already exists for this subject.',
            'subject_id.required' => 'Please select a subject.',
            'subject_id.exists' => 'The selected subject is invalid.',
        ]);

        SubjectModule::create($validated);

        return redirect()->route('subject-modules.create')->with('success', 'Subject module created successfully!');
    }

    public function edit(SubjectModule $subjectModule)
    {
        $subjects = Subject::all();

        return Inertia::render('SubjectModules/Edit', [
            'subjectModule' => $subjectModule,
            'subjects' => $subjects,
        ]);
    }

    public function update(Request $request, SubjectModule $subjectModule)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
        ], [
            'name.required' => 'Module name is required.',
            'subject_id.required' => 'Subject selection is required.',
            'subject_id.exists' => 'Selected subject does not exist.',
        ]);

        $subjectModule->update($validated);

        return redirect()->route('subject-modules.edit', $subjectModule->id)->with('success', 'Subject module updated successfully.');
    }

    public function destroy(SubjectModule $subjectModule)
    {
        $subjectModule->delete();

        return redirect()->route('subject-modules.index')->with('success', 'Subject module deleted successfully.');
    }
}
