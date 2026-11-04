<?php

namespace Tests\Unit;

use App\Services\CurriculumXpService;
use App\Services\QuantumRankService;
use Tests\TestCase;

class CurriculumXpServiceTest extends TestCase
{
    public function test_full_curriculum_units_awards_max_total_xp(): void
    {
        $ranks = app(QuantumRankService::class);
        $curriculum = app(CurriculumXpService::class);

        $curriculum->clearCachedTotals();
        $totalUnits = $curriculum->totalEngagementUnits();

        if ($totalUnits === 0) {
            $this->markTestSkipped('No lessons or module quizzes in the database.');
        }

        $expected = $ranks->maxTotalXp();
        $awarded = $curriculum->xpFromCompletedUnits($totalUnits);

        $this->assertSame($expected, $awarded);
        $this->assertSame(100, $ranks->levelFromTotalXp($awarded));
    }

    public function test_partial_progress_scales_linearly(): void
    {
        $curriculum = app(CurriculumXpService::class);
        $curriculum->clearCachedTotals();
        $totalUnits = $curriculum->totalEngagementUnits();

        if ($totalUnits < 4) {
            $this->markTestSkipped('Need at least four engagement units in the database.');
        }

        $half = (int) floor($totalUnits / 2);
        $full = $curriculum->xpFromCompletedUnits($totalUnits);
        $partial = $curriculum->xpFromCompletedUnits($half);

        $this->assertGreaterThan(0, $partial);
        $this->assertLessThan($full, $partial);
    }
}
