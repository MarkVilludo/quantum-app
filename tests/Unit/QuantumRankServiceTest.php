<?php

namespace Tests\Unit;

use App\Services\QuantumRankService;
use Tests\TestCase;

class QuantumRankServiceTest extends TestCase
{
    private QuantumRankService $ranks;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ranks = app(QuantumRankService::class);
    }

    public function test_xp_thresholds_for_early_levels(): void
    {
        $this->assertSame(0, $this->ranks->xpToReachLevel(1));
        $this->assertSame(5, $this->ranks->xpToReachLevel(2));
        $this->assertSame(11, $this->ranks->xpToReachLevel(3));
        $this->assertSame(18, $this->ranks->xpToReachLevel(4));
        $this->assertSame(26, $this->ranks->xpToReachLevel(5));
    }

    public function test_level_from_total_xp(): void
    {
        $this->assertSame(1, $this->ranks->levelFromTotalXp(0));
        $this->assertSame(1, $this->ranks->levelFromTotalXp(4));
        $this->assertSame(2, $this->ranks->levelFromTotalXp(5));
        $this->assertSame(3, $this->ranks->levelFromTotalXp(11));
        $this->assertSame(3, $this->ranks->levelFromTotalXp(17));
        $this->assertSame(4, $this->ranks->levelFromTotalXp(18));
    }

    public function test_level_capped_at_max(): void
    {
        $maxXp = $this->ranks->maxTotalXp();
        $this->assertSame(100, $this->ranks->levelFromTotalXp($maxXp));
        $this->assertSame(100, $this->ranks->levelFromTotalXp($maxXp + 10_000));
    }

    public function test_title_band_boundaries(): void
    {
        $this->assertSame('Quantum Initiate', $this->ranks->titleForLevel(2));
        $this->assertSame('Bit Scout', $this->ranks->titleForLevel(3));
        $this->assertSame('Syntax Adventurer', $this->ranks->titleForLevel(9));
        $this->assertSame('Module Runner', $this->ranks->titleForLevel(10));
    }

    public function test_high_level_uses_sage_title(): void
    {
        $this->assertSame('Quantum Sage', $this->ranks->titleForLevel(80));
        $this->assertSame('Quantum Sage', $this->ranks->titleForLevel(100));
        $xpForLevel100 = $this->ranks->xpToReachLevel(100);
        $this->assertSame(100, $this->ranks->levelFromTotalXp($xpForLevel100));
    }

    public function test_progress_in_level(): void
    {
        $progress = $this->ranks->progressFromTotalXp(7);
        $this->assertSame(2, $progress['level']);
        $this->assertSame(2, $progress['xp_in_level']);
        $this->assertSame(6, $progress['xp_for_next_level']);
        $this->assertSame(33, $progress['level_progress_percent']);
    }

    public function test_max_xp_shows_full_level_bar(): void
    {
        $maxXp = $this->ranks->maxTotalXp();
        $progress = $this->ranks->progressFromTotalXp($maxXp);
        $this->assertSame(100, $progress['level']);
        $this->assertSame($progress['xp_for_next_level'], $progress['xp_in_level']);
        $this->assertSame(100, $progress['level_progress_percent']);
    }

    public function test_profile_ladder_includes_next_level_and_catalog(): void
    {
        $ladder = $this->ranks->profileLadder(7);
        $this->assertSame(3, $ladder['next_level']['level']);
        $this->assertNotEmpty($ladder['levels']);
        $this->assertSame('current', $ladder['levels'][1]['state']);
    }

    public function test_profile_ladder_at_max_has_no_next_level(): void
    {
        $ladder = $this->ranks->profileLadder($this->ranks->maxTotalXp());
        $this->assertNull($ladder['next_level']);
        $this->assertSame(100, $ladder['max_level']);
    }
}
