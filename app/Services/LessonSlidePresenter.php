<?php

namespace App\Services;

class LessonSlidePresenter
{
    private const STEP_EMOJIS = ['📖', '💡', '🎯', '🔍', '✨', '🚀', '🧩', '⭐'];

    /**
     * @param  mixed  $data  Lesson JSON slides from CMS
     * @return list<array{step: int, title: string, content: string, emoji: string, total: int}>
     */
    public function normalizeSlides(mixed $data, string $lessonName = 'Lesson'): array
    {
        if (is_string($data)) {
            $decoded = json_decode($data, true);
            $data = is_array($decoded) ? $decoded : [];
        }
        if (! is_array($data)) {
            return [];
        }

        $rawSlides = [];
        foreach ($data as $row) {
            if (is_array($row)) {
                $content = trim((string) ($row['content'] ?? ''));
                $title = trim((string) ($row['title'] ?? ''));
            } else {
                $content = trim((string) $row);
                $title = '';
            }
            if ($content === '') {
                continue;
            }
            $rawSlides[] = ['title' => $title, 'content' => $content];
        }

        $total = count($rawSlides);
        $out = [];

        foreach ($rawSlides as $index => $slide) {
            $step = $index + 1;
            $title = $slide['title'] !== ''
                ? $slide['title']
                : $this->inferTitle($slide['content'], $step, $lessonName);

            $out[] = [
                'step' => $step,
                'title' => $title,
                'content' => $slide['content'],
                'emoji' => self::STEP_EMOJIS[$index % count(self::STEP_EMOJIS)],
                'total' => $total,
            ];
        }

        return $out;
    }

    private function inferTitle(string $content, int $step, string $lessonName): string
    {
        $firstLine = trim(strtok($content, "\n") ?: $content);
        if (strlen($firstLine) > 0 && strlen($firstLine) <= 72) {
            return rtrim($firstLine, '.');
        }

        return $lessonName . ' — Part ' . $step;
    }
}
