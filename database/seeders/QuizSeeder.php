<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\SubjectModule;
use App\Services\LessonProgressService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Seeds lesson_quizzes used by module quizzes (15 per module) and final subject quizzes (30 pooled per subject).
 *
 * Correct answer is always choice A ({@see QuizSeeder::CORRECT_ANSWER_KEY} → answer1).
 */
class QuizSeeder extends Seeder
{
    public const QUESTIONS_PER_MODULE = 15;

    public const CORRECT_ANSWER_KEY = 'answer1';

    private const MAX_LEN = 255;

    public function run(): void
    {
        $progress = app(LessonProgressService::class);

        $moduleQuizzesCreated = 0;
        $modulesSkipped = 0;
        $modulesWithoutLessons = 0;
        $subjectsVerified = 0;

        Subject::query()->orderBy('id')->chunkById(10, function ($subjects) use (
            $progress,
            &$moduleQuizzesCreated,
            &$modulesSkipped,
            &$modulesWithoutLessons,
            &$subjectsVerified
        ) {
            DB::transaction(function () use (
                $progress,
                $subjects,
                &$moduleQuizzesCreated,
                &$modulesSkipped,
                &$modulesWithoutLessons,
                &$subjectsVerified
            ) {
                foreach ($subjects as $subject) {
                    $modules = SubjectModule::query()
                        ->where('subject_id', $subject->id)
                        ->orderBy('id')
                        ->get();

                    foreach ($modules as $module) {
                        $result = $this->seedModuleQuizQuestions($module);
                        $moduleQuizzesCreated += $result['created'];
                        if ($result['skipped']) {
                            $modulesSkipped++;
                        }
                        if ($result['no_lessons']) {
                            $modulesWithoutLessons++;
                        }
                    }

                    $poolSize = $progress
                        ->finalSubjectQuizQuestions($subject->id, LessonProgressService::FINAL_SUBJECT_QUIZ_QUESTION_COUNT)
                        ->count();

                    if ($poolSize >= LessonProgressService::FINAL_SUBJECT_QUIZ_QUESTION_COUNT) {
                        $subjectsVerified++;
                    } else {
                        $this->command?->warn(
                            "Subject \"{$subject->name}\" (id {$subject->id}) has only {$poolSize} final-quiz questions; "
                            .LessonProgressService::FINAL_SUBJECT_QUIZ_QUESTION_COUNT.' expected after full module seeding.'
                        );
                    }
                }
            });
        });

        $this->command?->info("Module quiz questions created: {$moduleQuizzesCreated}.");
        $this->command?->info("Modules already at ".self::QUESTIONS_PER_MODULE.' questions: '.$modulesSkipped.'.');
        if ($modulesWithoutLessons > 0) {
            $this->command?->warn("Modules skipped (no lessons): {$modulesWithoutLessons}.");
        }
        $this->command?->info(
            'Subjects with a '.LessonProgressService::FINAL_SUBJECT_QUIZ_QUESTION_COUNT.'-question final quiz pool: '
            .$subjectsVerified.'.'
        );
        $this->command?->line('All seeded quizzes use correct answer A (answer1).');
    }

    /**
     * @return array{created: int, skipped: bool, no_lessons: bool}
     */
    private function seedModuleQuizQuestions(SubjectModule $module): array
    {
        $lessons = Lesson::query()
            ->where('module_id', $module->id)
            ->orderBy('id')
            ->get();

        if ($lessons->isEmpty()) {
            return ['created' => 0, 'skipped' => false, 'no_lessons' => true];
        }

        $lessonIds = $lessons->pluck('id');
        $existingCount = Quiz::query()->whereIn('lesson_id', $lessonIds)->count();

        if ($existingCount >= self::QUESTIONS_PER_MODULE) {
            return ['created' => 0, 'skipped' => true, 'no_lessons' => false];
        }

        $created = 0;
        $lessonCount = $lessons->count();

        for ($questionNumber = $existingCount + 1; $questionNumber <= self::QUESTIONS_PER_MODULE; $questionNumber++) {
            $lesson = $lessons[($questionNumber - 1) % $lessonCount];
            $slides = $this->normalizeSlides($lesson->data);
            if ($slides === []) {
                $slides = $this->fallbackSlides($lesson->name, $module->name);
            }

            $slideIndex = ($questionNumber - 1) % count($slides);
            $slideContent = $slides[$slideIndex];
            $correctText = $this->clip($slideContent);
            $distractors = $this->pickDistractors($slides, $slideIndex, $module->name);

            Quiz::create([
                'lesson_id' => $lesson->id,
                'question' => $this->buildQuestion($module->name, $lesson->name, $questionNumber, $slideContent),
                'answer1' => $correctText,
                'answer2' => $distractors[0],
                'answer3' => $distractors[1],
                'answer4' => $distractors[2],
                'correct' => self::CORRECT_ANSWER_KEY,
            ]);
            $created++;
        }

        return ['created' => $created, 'skipped' => false, 'no_lessons' => false];
    }

    /** @return list<string> */
    private function normalizeSlides(mixed $data): array
    {
        if (is_string($data)) {
            $decoded = json_decode($data, true);
            $data = is_array($decoded) ? $decoded : [];
        }
        if (! is_array($data)) {
            return [];
        }

        $slides = [];
        foreach ($data as $row) {
            if (is_array($row) && isset($row['content'])) {
                $text = trim((string) $row['content']);
            } elseif (is_string($row)) {
                $text = trim($row);
            } else {
                continue;
            }
            if ($text !== '') {
                $slides[] = $this->cleanText($text);
            }
        }

        return $slides;
    }

    /** @return list<string> */
    private function fallbackSlides(string $lessonName, string $moduleName): array
    {
        $slides = [];
        for ($i = 1; $i <= self::QUESTIONS_PER_MODULE; $i++) {
            $slides[] = "Core idea {$i} from {$lessonName} in module {$moduleName}.";
        }

        return $slides;
    }

    private function buildQuestion(string $moduleName, string $lessonName, int $number, string $slideContent): string
    {
        $snippet = $this->clip($slideContent, 120);
        $question = "({$moduleName} — Q{$number}) Which statement is correct? Choose A. Topic: {$lessonName}. \"{$snippet}\" "
            .'Explanation: option A matches the lesson material.';

        return $this->clip($question, self::MAX_LEN);
    }

    /** @param list<string> $slides */
    private function pickDistractors(array $slides, int $correctIndex, string $moduleName): array
    {
        $slideCount = count($slides);
        $correct = $this->clip($slides[$correctIndex] ?? '');
        $picked = [];

        if ($slideCount > 0) {
            for ($offset = 1; $offset <= 3; $offset++) {
                $idx = ($correctIndex + $offset) % $slideCount;
                $candidate = $this->clip($slides[$idx]);
                if ($candidate !== $correct && ! in_array($candidate, $picked, true)) {
                    $picked[] = $candidate;
                }
            }
        }

        $fallbacks = [
            "This does not apply to {$moduleName}.",
            'This contradicts the module learning objectives.',
            'This was not covered in this module section.',
        ];
        foreach ($fallbacks as $fallback) {
            if (count($picked) >= 3) {
                break;
            }
            if (! in_array($fallback, $picked, true)) {
                $picked[] = $fallback;
            }
        }

        return array_slice($picked, 0, 3);
    }

    private function cleanText(string $text): string
    {
        $text = preg_replace('/\*\*(.*?)\*\*/s', '$1', $text) ?? $text;
        $text = str_replace(['`', '*'], '', $text);
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }

    private function clip(string $text, int $max = self::MAX_LEN): string
    {
        $text = $this->cleanText($text);
        if (Str::length($text) <= $max) {
            return $text;
        }

        return Str::substr($text, 0, $max - 3).'...';
    }
}
