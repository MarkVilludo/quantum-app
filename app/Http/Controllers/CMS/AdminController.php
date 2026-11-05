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
            ->with('year')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
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

        User::create([
            'name' => $validated['name'],
            'email' => $email,
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'year_id' => $request->year_id,
        ]);

        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        return inertia('Users/Edit', [
            'user' => $user,
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
            ],
            'password' => 'nullable|min:8',
            'role' => 'required|in:student,teacher,admin',
        ]);

        $data['email'] = $email;

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        } else {
            unset($data['password']);
        }
        $user->year_id = $data['year_id'] ?? null;
        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    public function destroy(Request $request, User $user)
    {
        $actor = $request->user();

        if ($actor && (int) $actor->getKey() === (int) $user->getKey()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'You cannot delete your own account while signed in.');
        }

        if ($user->role === 'admin') {
            $otherAdmins = User::query()
                ->where('role', 'admin')
                ->whereKeyNot($user->getKey())
                ->count();

            if ($otherAdmins === 0) {
                return redirect()
                    ->route('users.index')
                    ->with('error', 'Cannot remove the only administrator account.');
            }
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted.');
    }
}
