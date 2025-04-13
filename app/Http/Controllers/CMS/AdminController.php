<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function users(Request $request)
    {
        $search = $request->input('search');

        $users = User::query()
            ->whereNot('role', 'admin')
            ->with('year')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->paginate(10)
            ->withQueryString(); // retain the search query in pagination links

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year_id' => 'nullable',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:teacher,student',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'year_id' => $request->year_id
        ]);
    }

    public function edit(User $user)
    {
        // Return the edit view with the user data
        return inertia('Users/Edit', [
            'user' => $user
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'year_id' => 'nullable',
            'email' => 'required|email|unique:users,email,' . $user->id,
            // Only validate the password if it's present
            'password' => 'nullable|min:8',
            'role' => 'required|in:student,teacher',
        ]);

        // If a new password is provided, update it
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        } else {
            // Remove password from the data if not provided
            unset($data['password']);
        }
        $user->year_id = $data['year_id'];
        $user->update($data);
    }

    public function destroy(User $user)
    {
        $user->delete();
    }
}
