<?php

namespace App\Services;

class QuantumRankService
{
    public function maxLevel(): int
    {
        return max(1, (int) config('quantum_rank.max_level', 100));
    }

    /** Total XP at level 100 with the level bar full (curriculum completion target). */
    public function maxTotalXp(): int
    {
        $cap = $this->maxLevel();

        return max(0, $this->xpToReachLevel($cap + 1) - 1);
    }

    public function xpDeltaForLevel(int $level): int
    {
        $base = (int) config('quantum_rank.base', 15);
        $step = (int) config('quantum_rank.step', 5);

        return $base + ($step * max(1, $level));
    }

    public function xpToReachLevel(int $level): int
    {
        if ($level <= 1) {
            return 0;
        }

        $total = 0;
        for ($L = 1; $L < $level; $L++) {
            $total += $this->xpDeltaForLevel($L);
        }

        return $total;
    }

    public function levelFromTotalXp(int $xp): int
    {
        $cap = $this->maxLevel();
        $level = 1;
        while ($level < $cap && $this->xpToReachLevel($level + 1) <= $xp) {
            $level++;
        }

        return $level;
    }

    /** @return array{level: int, title: string, title_band_min: int, xp_in_level: int, xp_for_next_level: int, level_progress_percent: int, total_xp: int, at_max_level: bool} */
    public function progressFromTotalXp(int $xp): array
    {
        $xp = max(0, min($xp, $this->maxTotalXp()));
        $level = $this->levelFromTotalXp($xp);
        $floor = $this->xpToReachLevel($level);
        $nextThreshold = $this->xpToReachLevel($level + 1);
        $delta = max(1, $nextThreshold - $floor);
        $atMaxLevel = $level >= $this->maxLevel();
        $atMaxXp = $xp >= $this->maxTotalXp();

        $rawInLevel = max(0, $xp - $floor);
        if ($atMaxLevel && $atMaxXp) {
            $xpInLevel = $delta;
        } else {
            $xpInLevel = min($delta, $rawInLevel);
        }

        $levelProgressPercent = (int) min(100, max(0, (int) floor(($xpInLevel / $delta) * 100)));

        return [
            'level' => $level,
            'title' => $this->titleForLevel($level),
            'title_band_min' => $this->titleBandMinForLevel($level),
            'xp_in_level' => $xpInLevel,
            'xp_for_next_level' => $delta,
            'level_progress_percent' => $levelProgressPercent,
            'total_xp' => $xp,
            'at_max_level' => $atMaxLevel,
        ];
    }

    public function titleForLevel(int $level): string
    {
        $level = max(1, $level);

        foreach (config('quantum_rank.bands', []) as $band) {
            $min = (int) ($band['min'] ?? 1);
            $max = $band['max'] ?? null;
            if ($level < $min) {
                continue;
            }
            if ($max === null || $level <= (int) $max) {
                return (string) ($band['title'] ?? 'Quantum Explorer');
            }
        }

        return 'Quantum Explorer';
    }

    public function titleBandMinForLevel(int $level): int
    {
        $level = max(1, $level);

        foreach (config('quantum_rank.bands', []) as $band) {
            $min = (int) ($band['min'] ?? 1);
            $max = $band['max'] ?? null;
            if ($level < $min) {
                continue;
            }
            if ($max === null || $level <= (int) $max) {
                return $min;
            }
        }

        return 1;
    }

    /**
     * @return array{
     *     next_level: array{level: int, title: string, total_xp_required: int, xp_remaining: int},
     *     xp_rewards: list<array{label: string, xp: string}>,
     *     title_bands: list<array{min_level: int, max_level: int|null, title: string, total_xp_at_min: int}>,
     *     levels: list<array{level: int, title: string, total_xp: int, xp_for_level: int, state: string}>
     * }
     */
    public function profileLadder(int $totalXp): array
    {
        $totalXp = max(0, $totalXp);
        $progress = $this->progressFromTotalXp($totalXp);
        $currentLevel = $progress['level'];
        $nextLevel = $currentLevel + 1;
        $nextThreshold = $this->xpToReachLevel($nextLevel);

        $through = max(1, (int) config('quantum_rank.profile_ladder_through_level', 100));
        $extend = max(0, (int) config('quantum_rank.profile_ladder_extend_above', 15));
        $levelCap = $this->maxLevel();
        $maxLevel = min($levelCap, max($through, $currentLevel + $extend));

        $levels = [];
        for ($level = 1; $level <= $maxLevel; $level++) {
            $threshold = $this->xpToReachLevel($level);
            $xpForLevel = $level === 1 ? 0 : $this->xpDeltaForLevel($level - 1);
            $state = 'upcoming';
            if ($level < $currentLevel) {
                $state = 'attained';
            } elseif ($level === $currentLevel) {
                $state = 'current';
            }

            $xpRemaining = max(0, $threshold - $totalXp);
            $levels[] = [
                'level' => $level,
                'title' => $this->titleForLevel($level),
                'total_xp' => $threshold,
                'xp_for_level' => $xpForLevel,
                'xp_remaining' => $xpRemaining,
                'state' => $state,
                'reward' => 'Rank title: '.$this->titleForLevel($level),
                'requirement' => $level === 1
                    ? 'Every student starts at Level 1 with 0 Quantum XP.'
                    : 'Reach '.$threshold.' total Quantum XP (+'.$xpForLevel.' XP to advance from Level '.($level - 1).').',
            ];
        }

        $titleBands = [];
        foreach (config('quantum_rank.bands', []) as $band) {
            $min = (int) ($band['min'] ?? 1);
            $max = isset($band['max']) ? (int) $band['max'] : null;
            $xpAtMin = $this->xpToReachLevel($min);
            if ($currentLevel < $min) {
                $state = 'locked';
            } elseif ($max === null || $currentLevel <= $max) {
                $state = 'current';
            } else {
                $state = 'unlocked';
            }
            $xpRemaining = $state === 'locked' ? max(0, $xpAtMin - $totalXp) : 0;
            $progressPercent = 100;
            if ($state === 'locked') {
                $progressPercent = $min === 1 ? 0 : (int) min(99, floor(($totalXp / max(1, $xpAtMin)) * 100));
            } elseif ($state === 'current' && $max !== null) {
                if ($totalXp >= $this->maxTotalXp()) {
                    $progressPercent = 100;
                } else {
                    $xpAtMaxPlus = $this->xpToReachLevel($max + 1);
                    $span = max(1, $xpAtMaxPlus - $xpAtMin);
                    $progressPercent = (int) min(100, max(0, floor((($totalXp - $xpAtMin) / $span) * 100)));
                }
            }

            $titleBands[] = [
                'min_level' => $min,
                'max_level' => $max,
                'title' => (string) ($band['title'] ?? 'Quantum Explorer'),
                'total_xp_at_min' => $xpAtMin,
                'state' => $state,
                'xp_remaining' => $xpRemaining,
                'progress_percent' => $progressPercent,
                'reward' => 'Display title: '.((string) ($band['title'] ?? 'Quantum Explorer')),
                'requirement' => $max === null
                    ? 'Reach level '.$min.' ('.$xpAtMin.' total Quantum XP) and beyond.'
                    : 'Reach levels '.$min.'–'.$max.' ('.$xpAtMin.'+ total Quantum XP).',
            ];
        }

        $nextLevelPayload = null;
        if ($currentLevel < $this->maxLevel()) {
            $nextLevelPayload = [
                'level' => $nextLevel,
                'title' => $this->titleForLevel($nextLevel),
                'total_xp_required' => $nextThreshold,
                'total_xp' => $nextThreshold,
                'xp_remaining' => max(0, $nextThreshold - $totalXp),
                'xp_for_level' => $this->xpDeltaForLevel($nextLevel - 1),
                'state' => 'upcoming',
                'reward' => 'Rank title: '.$this->titleForLevel($nextLevel),
                'requirement' => 'Reach '.$nextThreshold.' total Quantum XP (+'.$this->xpDeltaForLevel($nextLevel - 1).' XP from your current level).',
            ];
        }

        $xpRewards = app(CurriculumXpService::class)->xpRewardLabels();

        return [
            'next_level' => $nextLevelPayload,
            'max_level' => $this->maxLevel(),
            'max_total_xp' => $this->maxTotalXp(),
            'xp_rewards' => $xpRewards,
            'title_bands' => $titleBands,
            'levels' => $levels,
        ];
    }
}
