<?php

namespace App\Services;

use App\Models\GraduationCompletion;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class GraduationService
{
    public function __construct(private LessonProgressService $progress)
    {
    }

    public function isFullyGraduated(int $userId): bool
    {
        $years = $this->progress->orderedYears();
        if ($years->isEmpty()) {
            return false;
        }

        foreach ($years as $year) {
            if (! $this->progress->isYearCompleted($userId, $year->id)) {
                return false;
            }
        }

        return true;
    }

    public function completionForUser(int $userId): ?GraduationCompletion
    {
        return GraduationCompletion::query()->where('user_id', $userId)->first();
    }

    /**
     * Record graduation when eligible. Returns payload for API clients.
     *
     * @return array<string, mixed>|null
     */
    public function processAfterFinalQuizPass(User $user): ?array
    {
        if (! $this->isFullyGraduated($user->id)) {
            return null;
        }

        $existing = $this->completionForUser($user->id);
        $newlyGraduated = $existing === null;

        $completion = $existing ?? GraduationCompletion::create([
            'user_id' => $user->id,
            'certificate_code' => $this->generateCertificateCode($user),
            'graduated_at' => now(),
        ]);

        return [
            'newly_graduated' => $newlyGraduated,
            'graduated_at' => $completion->graduated_at?->toIso8601String(),
            'certificate_code' => $completion->certificate_code,
            'student_name' => $user->name,
        ];
    }

    /** @return array<string, mixed> */
    public function statusPayloadForUser(User $user): array
    {
        $eligible = $this->isFullyGraduated($user->id);
        $completion = $this->completionForUser($user->id);

        if ($eligible && $completion === null) {
            try {
                $this->processAfterFinalQuizPass($user);
                $completion = $this->completionForUser($user->id);
            } catch (Throwable $e) {
                report($e);
            }
        }

        return [
            'eligible' => $eligible,
            'graduated' => $eligible,
            'graduated_at' => $completion?->graduated_at?->toIso8601String(),
            'certificate_code' => $completion?->certificate_code,
            'student_name' => $user->name,
            'program_title' => 'Quantum Realm Programming Curriculum',
        ];
    }

    public function certificateHtml(User $user): string
    {
        $eligible = $this->isFullyGraduated($user->id);
        if (! $eligible) {
            abort(403, 'Certificate is not available until all year-level final quizzes are passed.');
        }

        $completion = $this->ensureCompletionWhenEligible($user, true);
        if (! $completion) {
            abort(403, 'Certificate is not available until all year-level final quizzes are passed.');
        }

        return view('graduation.certificate', [
            'studentName' => $user->name,
            'graduatedAt' => $completion->graduated_at,
            'certificateCode' => $completion->certificate_code,
            'programTitle' => 'Quantum Realm — Learning Galaxy',
        ])->render();
    }

    public function ensureRecordedForDev(User $user): void
    {
        if (! $this->isFullyGraduated($user->id)) {
            return;
        }

        if ($this->completionForUser($user->id) === null) {
            GraduationCompletion::create([
                'user_id' => $user->id,
                'certificate_code' => $this->generateCertificateCode($user),
                'graduated_at' => now(),
            ]);
        }
    }

    private function generateCertificateCode(User $user): string
    {
        return 'QR-'.now()->format('Y').'-'.strtoupper(Str::random(8)).'-'.$user->id;
    }

    private function ensureCompletionWhenEligible(User $user, bool $eligible): ?GraduationCompletion
    {
        if (! $eligible) {
            return null;
        }

        $completion = $this->completionForUser($user->id);
        if ($completion !== null) {
            return $completion;
        }

        $this->processAfterFinalQuizPass($user);

        return $this->completionForUser($user->id);
    }
}
