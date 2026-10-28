<?php

namespace App\Services;

use App\Models\LessonQuizResult;
use App\Models\ModuleQuizResult;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\SubjectModule;
use App\Models\SubjectQuizResult;
use App\Models\User;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CmsStudentQuizResultsService
{
    public function paginate(Request $request, User $viewer): LengthAwarePaginator
    {
        $filters = $this->normalizeFilters($request, $viewer);
        $page = max(1, (int) $request->get('page', 1));
        $perPage = 10;

        $rows = $this->collectRows($viewer, $filters);
        $total = $rows->count();
        $slice = $rows->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $slice,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }

    /** @return array<string, mixed> */
    public function filterOptions(User $viewer, array $filters): array
    {
        $yearScope = $this->viewerYearScope($viewer, $filters['year_id'] ?? null);

        $years = $viewer->role === 'admin'
            ? Year::query()->orderBy('id')->get(['id', 'name'])
            : Year::query()->where('id', $viewer->year_id)->get(['id', 'name']);

        $subjectsQuery = Subject::query()->with('year')->orderBy('name');
        if ($yearScope) {
            $subjectsQuery->where('year_id', $yearScope);
        }
        $subjects = $subjectsQuery->get(['id', 'name', 'year_id']);

        $modulesQuery = SubjectModule::query()->orderBy('name');
        if (!empty($filters['subject_id'])) {
            $modulesQuery->where('subject_id', $filters['subject_id']);
        } elseif ($yearScope) {
            $modulesQuery->whereHas('subject', fn ($q) => $q->where('year_id', $yearScope));
        }
        $modules = $modulesQuery->get(['id', 'name', 'subject_id']);

        $studentsQuery = User::query()
            ->where('role', 'student')
            ->with('year')
            ->orderBy('name');
        if ($yearScope) {
            $studentsQuery->where('year_id', $yearScope);
        }
        $students = $studentsQuery->get(['id', 'name', 'year_id']);

        return [
            'years' => $years,
            'subjects' => $subjects,
            'modules' => $modules,
            'students' => $students,
        ];
    }

    /** @return array<string, mixed> */
    private function normalizeFilters(Request $request, User $viewer): array
    {
        $filters = $request->only(['search', 'user_id', 'year_id', 'subject_id', 'module_id', 'quiz_type']);

        if ($viewer->role === 'teacher' && $viewer->year_id) {
            $filters['year_id'] = (string) $viewer->year_id;
        }

        if ($viewer->role === 'student') {
            $filters['user_id'] = (string) $viewer->id;
        }

        return $filters;
    }

    private function viewerYearScope(User $viewer, ?string $requestedYearId): ?int
    {
        if ($viewer->role === 'teacher' && $viewer->year_id) {
            return (int) $viewer->year_id;
        }

        if ($requestedYearId !== null && $requestedYearId !== '') {
            return (int) $requestedYearId;
        }

        return null;
    }

    /** @param array<string, mixed> $filters */
    private function collectRows(User $viewer, array $filters): Collection
    {
        $rows = collect();

        $quizType = $filters['quiz_type'] ?? '';
        $includeModule = $quizType === '' || $quizType === 'module';
        $includeSubjectFinal = $quizType === '' || $quizType === 'subject_final';
        $includeLessonLegacy = $quizType === '' || $quizType === 'lesson_legacy';

        if ($includeModule) {
            $rows = $rows->merge($this->moduleRows($viewer, $filters));
        }
        if ($includeSubjectFinal) {
            $rows = $rows->merge($this->subjectFinalRows($viewer, $filters));
        }
        if ($includeLessonLegacy) {
            $rows = $rows->merge($this->lessonLegacyRows($viewer, $filters));
        }

        return $rows->sortByDesc('recorded_at')->values();
    }

    /** @param array<string, mixed> $filters */
    private function moduleRows(User $viewer, array $filters): Collection
    {
        $query = ModuleQuizResult::query()
            ->with(['user.year', 'module.subject.year']);

        $this->applyStudentScope($query, $viewer, $filters);

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['module_id'])) {
            $query->where('module_id', $filters['module_id']);
        }
        if (!empty($filters['subject_id'])) {
            $query->whereHas('module', fn ($q) => $q->where('subject_id', $filters['subject_id']));
        }

        return $query->get()->map(function (ModuleQuizResult $result) use ($filters) {
            $module = $result->module;
            $subject = $module?->subject;
            $user = $result->user;
            $yearName = $user?->year?->name ?? $subject?->year?->name ?? '—';

            $row = [
                'id' => 'module-' . $result->id,
                'quiz_type' => 'module',
                'quiz_type_label' => 'Module quiz',
                'user' => [
                    'id' => $user?->id,
                    'name' => $user?->name ?? 'Unknown',
                ],
                'year_name' => $yearName,
                'subject_name' => $subject?->name ?? '—',
                'module_name' => $module?->name ?? '—',
                'score' => (int) $result->score,
                'total' => (int) $result->total,
                'percent' => $result->total > 0 ? (int) round(($result->score / $result->total) * 100) : 0,
                'passed' => (bool) $result->passed,
                'answers' => $this->formatAnswerDetails($result->answers),
                'recorded_at' => $result->updated_at?->timestamp ?? 0,
            ];

            return $this->matchesSearch($row, $filters['search'] ?? null) ? $row : null;
        })->filter()->values();
    }

    /** @param array<string, mixed> $filters */
    private function subjectFinalRows(User $viewer, array $filters): Collection
    {
        if (!empty($filters['module_id'])) {
            return collect();
        }

        $query = SubjectQuizResult::query()
            ->with(['user.year', 'subject.year']);

        $this->applyStudentScope($query, $viewer, $filters);

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['subject_id'])) {
            $query->where('subject_id', $filters['subject_id']);
        }

        return $query->get()->map(function (SubjectQuizResult $result) use ($filters) {
            $subject = $result->subject;
            $user = $result->user;
            $yearName = $user?->year?->name ?? $subject?->year?->name ?? '—';

            $row = [
                'id' => 'subject-' . $result->id,
                'quiz_type' => 'subject_final',
                'quiz_type_label' => 'Final subject quiz',
                'user' => [
                    'id' => $user?->id,
                    'name' => $user?->name ?? 'Unknown',
                ],
                'year_name' => $yearName,
                'subject_name' => $subject?->name ?? '—',
                'module_name' => '—',
                'score' => (int) $result->score,
                'total' => (int) $result->total,
                'percent' => $result->total > 0 ? (int) round(($result->score / $result->total) * 100) : 0,
                'passed' => (bool) $result->passed,
                'answers' => $this->formatAnswerDetails($result->answers),
                'recorded_at' => $result->updated_at?->timestamp ?? 0,
            ];

            return $this->matchesSearch($row, $filters['search'] ?? null) ? $row : null;
        })->filter()->values();
    }

    /** @param array<string, mixed> $filters */
    private function lessonLegacyRows(User $viewer, array $filters): Collection
    {
        if (!empty($filters['module_id'])) {
            return collect();
        }

        $query = LessonQuizResult::query()
            ->with(['user.year', 'lesson.subject.year']);

        $this->applyStudentScope($query, $viewer, $filters);

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['subject_id'])) {
            $query->whereHas('lesson', fn ($q) => $q->where('subject_id', $filters['subject_id']));
        }

        return $query->get()->map(function (LessonQuizResult $result) use ($filters) {
            $lesson = $result->lesson;
            $subject = $lesson?->subject;
            $user = $result->user;
            $yearName = $user?->year?->name ?? $lesson?->year?->name ?? $subject?->year?->name ?? '—';

            $row = [
                'id' => 'lesson-' . $result->id,
                'quiz_type' => 'lesson_legacy',
                'quiz_type_label' => 'Lesson quiz (legacy)',
                'user' => [
                    'id' => $user?->id,
                    'name' => $user?->name ?? 'Unknown',
                ],
                'year_name' => $yearName,
                'subject_name' => $subject?->name ?? '—',
                'module_name' => $lesson?->name ?? '—',
                'score' => (int) $result->score,
                'total' => (int) $result->total,
                'percent' => $result->total > 0 ? (int) round(($result->score / $result->total) * 100) : 0,
                'passed' => null,
                'answers' => $this->formatAnswerDetails($result->answers),
                'recorded_at' => $result->updated_at?->timestamp ?? 0,
            ];

            return $this->matchesSearch($row, $filters['search'] ?? null) ? $row : null;
        })->filter()->values();
    }

    /** @param array<string, mixed> $filters */
    private function applyStudentScope($query, User $viewer, array $filters): void
    {
        $yearScope = $this->viewerYearScope($viewer, $filters['year_id'] ?? null);

        if ($viewer->role === 'student') {
            $query->where('user_id', $viewer->id);

            return;
        }

        if ($yearScope) {
            $query->whereHas('user', fn ($q) => $q->where('year_id', $yearScope));
        }
    }

    /** @param array<string, mixed> $row */
    private function matchesSearch(array $row, ?string $search): bool
    {
        if ($search === null || trim($search) === '') {
            return true;
        }

        $needle = strtolower(trim($search));
        $haystack = strtolower(implode(' ', [
            $row['user']['name'] ?? '',
            $row['year_name'] ?? '',
            $row['subject_name'] ?? '',
            $row['module_name'] ?? '',
            $row['quiz_type_label'] ?? '',
        ]));

        return str_contains($haystack, $needle);
    }

    /** @param mixed $answers */
    private function formatAnswerDetails($answers): array
    {
        if (!is_array($answers) || $answers === []) {
            return [];
        }

        $ids = collect($answers)->pluck('quiz_id')->filter()->unique()->values();
        $quizzes = Quiz::whereIn('id', $ids)->get()->keyBy('id');

        return collect($answers)->map(function ($answer) use ($quizzes) {
            if (!is_array($answer)) {
                return null;
            }
            $quiz = $quizzes->get($answer['quiz_id'] ?? 0);
            $correctText = $quiz ? $this->correctAnswerText($quiz) : (string) ($answer['correct'] ?? 'N/A');
            $storedCorrect = $answer['correct'] ?? null;
            if (is_string($storedCorrect) && str_starts_with(strtolower($storedCorrect), 'answer')) {
                $displayCorrect = $correctText;
            } else {
                $displayCorrect = (string) ($storedCorrect ?? $correctText);
            }

            $selected = (string) ($answer['selected'] ?? '');
            $isCorrect = array_key_exists('is_correct', $answer)
                ? (bool) $answer['is_correct']
                : ($selected !== '' && $selected === $correctText);

            return [
                'quiz_id' => $answer['quiz_id'] ?? null,
                'question' => $quiz?->question ?? 'Question',
                'selected' => $selected,
                'correct' => $displayCorrect,
                'is_correct' => $isCorrect,
            ];
        })->filter()->values()->all();
    }

    private function correctAnswerText(Quiz $quiz): string
    {
        $key = strtolower((string) $quiz->correct);

        return match ($key) {
            'answer1' => (string) $quiz->answer1,
            'answer2' => (string) $quiz->answer2,
            'answer3' => (string) $quiz->answer3,
            'answer4' => (string) $quiz->answer4,
            default => (string) $quiz->correct,
        };
    }
}
