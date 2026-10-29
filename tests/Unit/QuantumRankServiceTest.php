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
        $this->assertSame(20, $this->ranks->xpToReachLevel(2));
        $this->assertSame(45, $this->ranks->xpToReachLevel(3));
        $this->assertSame(75, $this->ranks->xpToReachLevel(4));
        $this->assertSame(110, $this->ranks->xpToReachLevel(5));
    }

    public function test_level_from_total_xp(): void
    {
        $this->assertSame(1, $this->ranks->levelFromTotalXp(0));
        $this->assertSame(1, $this->ranks->levelFromTotalXp(19));
        $this->assertSame(2, $this->ranks->levelFromTotalXp(20));
        $this->assertSame(3, $this->ranks->levelFromTotalXp(45));
        $this->assertSame(3, $this->ranks->levelFromTotalXp(74));
        $this->assertSame(4, $this->ranks->levelFromTotalXp(75));
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
        $progress = $this->ranks->progressFromTotalXp(25);
        $this->assertSame(2, $progress['level']);
        $this->assertSame(5, $progress['xp_in_level']);
        $this->assertSame(25, $progress['xp_for_next_level']);
    }

    public function test_profile_ladder_includes_next_level_and_catalog(): void
    {
        $ladder = $this->ranks->profileLadder(25);
        $this->assertSame(3, $ladder['next_level']['level']);
        $this->assertSame(20, $ladder['next_level']['xp_remaining']);
        $this->assertNotEmpty($ladder['levels']);
        $this->assertSame('current', $ladder['levels'][1]['state']);
    }
}
