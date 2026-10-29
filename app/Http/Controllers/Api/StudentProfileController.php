<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ModuleQuizResult;
use App\Models\SubjectQuizResult;
use App\Services\GraduationService;
use App\Services\StudentEngagementService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StudentProfileController extends Controller
{
    public function __construct(
        private StudentEngagementService $engagement,
        private GraduationService $graduation,
    ) {
    }

    public function show(Request $request)
    {
        try {
            return $this->buildProfileResponse($request);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Could not load profile.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function buildProfileResponse(Request $request)
    {
        $user = $request->user();

        $moduleResults = ModuleQuizResult::query()
            ->where('user_id', $user->id)
            ->with(['module.subject'])
            ->orderBy('module_id')
            ->get();

        $subjectFinalResults = SubjectQuizResult::query()
            ->where('user_id', $user->id)
            ->with('subject')
            ->orderBy('subject_id')
            ->get()
            ->keyBy('subject_id');

        $bySubject = [];

        foreach ($moduleResults as $result) {
            $module = $result->module;
            if (!$module) {
                continue;
            }
            $subject = $module->subject;
            $subjectId = $module->subject_id;
            $subjectName = $subject?->name ?? 'Subject #' . $subjectId;

            if (!isset($bySubject[$subjectId])) {
                $bySubject[$subjectId] = [
                    'subject_id' => $subjectId,
                    'subject_name' => $subjectName,
                    'module_quizzes' => [],
                    'final_quiz' => null,
                ];
            }

            $total = max(1, (int) $result->total);
            $score = (int) $result->score;

            $bySubject[$subjectId]['module_quizzes'][] = [
                'module_id' => $module->id,
                'module_name' => $module->name,
                'score' => $score,
                'total' => $total,
                'percent' => (int) round(($score / $total) * 100),
                'passed' => (bool) $result->passed,
            ];
        }

        foreach ($subjectFinalResults as $subjectId => $final) {
            if (!isset($bySubject[$subjectId])) {
                $bySubject[$subjectId] = [
                    'subject_id' => $subjectId,
                    'subject_name' => $final->subject?->name ?? 'Subject #' . $subjectId,
                    'module_quizzes' => [],
                    'final_quiz' => null,
                ];
            }

            $total = max(1, (int) $final->total);
            $score = (int) $final->score;

            $bySubject[$subjectId]['final_quiz'] = [
                'score' => $score,
                'total' => $total,
                'percent' => (int) round(($score / $total) * 100),
                'passed' => (bool) $final->passed,
            ];
        }

        ksort($bySubject);

        try {
            $badges = $this->engagement->badgesForUser($user->id);
        } catch (\Throwable $e) {
            report($e);
            $badges = [];
        }

        try {
            $graduation = $this->graduation->statusPayloadForUser($user);
        } catch (\Throwable $e) {
            report($e);
            $graduation = [
                'eligible' => false,
                'graduated' => false,
                'student_name' => $user->name,
                'program_title' => 'Quantum Realm Programming Curriculum',
            ];
        }

        try {
            $dashboard = $this->engagement->dashboard($user->id);
            if (is_array($dashboard) && isset($dashboard['rank_ladder']['levels'])) {
                unset($dashboard['rank_ladder']['levels']);
            }
        } catch (\Throwable $e) {
            report($e);
            $dashboard = null;
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'dashboard' => $dashboard,
            'graduation' => $graduation,
            'badges' => $badges,
            'quiz_scores_by_subject' => array_values($bySubject),
        ], Response::HTTP_OK);
    }
}
