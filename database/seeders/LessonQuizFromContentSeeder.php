<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\Quiz;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Generates exactly 15 CMS lesson quizzes per module lesson (cycles content slides when fewer than 15).
 *
 * Run manually (does not alter default DatabaseSeeder):
 *   php artisan db:seed --class=LessonQuizFromContentSeeder
 */
class LessonQuizFromContentSeeder extends Seeder
{
    private const QUESTIONS_PER_LESSON = 15;

    private const MAX_LEN = 255;

    public function run(): void
    {
        $lessons = Lesson::query()->orderBy('id')->get();
        $created = 0;
        $skipped = 0;

        foreach ($lessons as $lesson) {
            $existing = $lesson->quizzes()->count();
            if ($existing >= self::QUESTIONS_PER_LESSON) {
                $skipped++;
                continue;
            }

            $slides = $this->normalizeSlides($lesson->data);
            if ($slides === []) {
                $slides = $this->fallbackSlides($lesson->name);
            }

            for ($questionNumber = $existing + 1; $questionNumber <= self::QUESTIONS_PER_LESSON; $questionNumber++) {
                $slideIndex = ($questionNumber - 1) % count($slides);
                $slideContent = $slides[$slideIndex];
                $correctText = $this->clip($slideContent);
                $distractors = $this->pickDistractors($slides, $slideIndex);

                Quiz::create([
                    'lesson_id' => $lesson->id,
                    'question' => $this->buildQuestion($lesson->name, $questionNumber, $slideContent),
                    'answer1' => $correctText,
                    'answer2' => $distractors[0],
                    'answer3' => $distractors[1],
                    'answer4' => $distractors[2],
                    'correct' => 'answer1',
                ]);
                $created++;
            }
        }

        $this->command?->info("Lesson quizzes created: {$created}. Lessons already at 15 questions: {$skipped}.");
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
    private function fallbackSlides(string $lessonName): array
    {
        $slides = [];
        for ($i = 1; $i <= self::QUESTIONS_PER_LESSON; $i++) {
            $slides[] = "Key concept {$i} for {$lessonName}.";
        }

        return $slides;
    }

    private function buildQuestion(string $lessonName, int $number, string $slideContent): string
    {
        $snippet = $this->clip($slideContent, 160);
        $question = "({$lessonName} — question {$number}) Which statement is correct? \"{$snippet}\"";

        return $this->clip($question, self::MAX_LEN);
    }

    /** @param list<string> $slides */
    private function pickDistractors(array $slides, int $correctIndex): array
    {
        $slideCount = count($slides);
        if ($slideCount === 0) {
            return [
                'Not covered in this lesson.',
                'Contradicts the lesson material.',
                'Unrelated to this topic.',
            ];
        }

        $correct = $this->clip($slides[$correctIndex]);
        $picked = [];

        for ($offset = 1; $offset <= 3; $offset++) {
            $idx = ($correctIndex + $offset) % $slideCount;
            $candidate = $this->clip($slides[$idx]);
            if ($candidate !== $correct && ! in_array($candidate, $picked, true)) {
                $picked[] = $candidate;
            }
        }

        $fallbacks = [
            'This statement does not match the lesson content.',
            'This is incorrect for this lesson section.',
            'Not stated in this lesson point.',
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
