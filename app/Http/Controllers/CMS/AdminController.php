<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function users(Request $request)
    {
        $search = $request->input('search');

        $users = User::query()
            ->withTrashed()
            ->with('year')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('deleted_at')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

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
        $email = User::normalizeEmail($request->input('email', ''));

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year_id' => 'nullable',
            'email' => [
                'required',
                'email',
                function (string $attribute, mixed $value, \Closure $fail) use ($email): void {
                    if ($email === '') {
                        return;
                    }
                    $existing = User::query()
                        ->whereRaw('LOWER(email) = ?', [$email])
                        ->first(['id', 'email', 'role']);
                    if ($existing !== null) {
                        $fail(sprintf(
                            'The email has already been taken (%s account — search Users for "%s" or edit user #%d).',
                            $existing->role,
                            $existing->email,
                            $existing->id
                        ));
                    }
                },
            ],
            'password' => 'required|string|min:8',
            'role' => 'required|in:teacher,student',
        ]);

        User::onlyTrashed()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->forceDelete();

        User::create([
            'name' => $validated['name'],
            'email' => $email,
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'year_id' => $request->year_id,
        ]);

        return redirect()->route('users.index');
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
        $email = User::normalizeEmail($request->input('email', ''));

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'year_id' => 'nullable',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
                function (string $attribute, mixed $value, \Closure $fail) use ($email, $user): void {
                    if ($email === '') {
                        return;
                    }
                    $conflict = User::withTrashed()
                        ->whereRaw('LOWER(email) = ?', [$email])
                        ->where('id', '!=', $user->id)
                        ->exists();
                    if ($conflict) {
                        $fail('The email has already been taken.');
                    }
                },
            ],
            'password' => 'nullable|min:8',
            'role' => 'required|in:student,teacher,admin',
        ]);

        User::onlyTrashed()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->where('id', '!=', $user->id)
            ->forceDelete();

        $data['email'] = $email;

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        } else {
            unset($data['password']);
        }
        $user->year_id = $data['year_id'] ?? null;
        $user->update($data);

        return redirect()->route('users.index');
    }

    public function destroy(User $user)
    {
        if ($user->trashed()) {
            $user->forceDelete();
        } else {
            $user->delete();
        }

        return redirect()->route('users.index');
    }

    public function restore(User $user): \Illuminate\Http\RedirectResponse
    {
        if ($user->trashed()) {
            $user->restore();
        }

        return redirect()->route('users.index');
    }
}
