<?php

namespace App\Services;

use App\Models\User;
use App\Models\Year;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class CmsStudentProgressionService
{
    public function __construct(private QuantumRankService $ranks)
    {
    }

    public function progressionPayloadForUser(User $user): array
    {
        $totalXp = (int) $user->quantum_xp;
        $rank = $this->ranks->progressFromTotalXp($totalXp);
        $ladder = $this->ranks->profileLadder($totalXp);

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'quantum_xp' => $totalXp,
            'rank' => $rank,
            'rank_ladder' => $ladder,
        ];
    }

    public function paginateStudents(Request $request, User $viewer): LengthAwarePaginator
    {
        $search = trim((string) $request->input('search', ''));
        $yearId = $request->input('year_id');

        if ($viewer->role === 'teacher' && $viewer->year_id) {
            $yearId = (string) $viewer->year_id;
        }

        $query = User::query()
            ->where('role', 'student')
            ->with('year:id,name')
            ->orderBy('name');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        if ($yearId !== null && $yearId !== '') {
            $query->where('year_id', (int) $yearId);
        }

        $paginator = $query->paginate(15)->withQueryString();

        $paginator->getCollection()->transform(function (User $student) {
            $totalXp = (int) $student->quantum_xp;
            $rank = $this->ranks->progressFromTotalXp($totalXp);

            return [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'year_id' => $student->year_id,
                'year_name' => $student->year?->name ?? '—',
                'quantum_xp' => $totalXp,
                'level' => $rank['level'],
                'title' => $rank['title'],
                'xp_in_level' => $rank['xp_in_level'],
                'xp_for_next_level' => $rank['xp_for_next_level'],
            ];
        });

        return $paginator;
    }

    /** @return array{years: \Illuminate\Support\Collection, students: \Illuminate\Support\Collection} */
    public function filterOptions(User $viewer): array
    {
        $studentQuery = User::query()->where('role', 'student')->orderBy('name');
        if ($viewer->role === 'teacher' && $viewer->year_id) {
            $studentQuery->where('year_id', $viewer->year_id);
        }

        $years = Year::orderBy('id')->get(['id', 'name']);
        if ($viewer->role === 'teacher' && $viewer->year_id) {
            $years = $years->where('id', $viewer->year_id)->values();
        }

        return [
            'years' => $years,
            'students' => $studentQuery->get(['id', 'name']),
        ];
    }
}
