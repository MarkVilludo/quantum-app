<?php

return [
    // Lower base/step so finishing all 4 years (~1.7k+ XP) lands around level 50–60.
    'base' => (int) env('QUANTUM_RANK_BASE', 4),
    'step' => (int) env('QUANTUM_RANK_STEP', 1),

    /** Levels 1..N always listed on the student profile rank ladder. */
    'profile_ladder_through_level' => (int) env('QUANTUM_RANK_PROFILE_LADDER_THROUGH', 70),

    /** Extra levels shown beyond the student's current level. */
    'profile_ladder_extend_above' => (int) env('QUANTUM_RANK_PROFILE_LADDER_EXTEND', 15),

    'bands' => [
        ['min' => 1, 'max' => 2, 'title' => 'Quantum Initiate'],
        ['min' => 3, 'max' => 5, 'title' => 'Bit Scout'],
        ['min' => 6, 'max' => 9, 'title' => 'Syntax Adventurer'],
        ['min' => 10, 'max' => 14, 'title' => 'Module Runner'],
        ['min' => 15, 'max' => 19, 'title' => 'Debug Ranger'],
        ['min' => 20, 'max' => 24, 'title' => 'Algorithm Knight'],
        ['min' => 25, 'max' => 29, 'title' => 'Stack Voyager'],
        ['min' => 30, 'max' => 39, 'title' => 'Systems Architect'],
        ['min' => 40, 'max' => 49, 'title' => 'Quantum Engineer'],
        ['min' => 50, 'max' => 59, 'title' => 'Realm Guardian'],
        ['min' => 60, 'max' => 79, 'title' => 'Dimension Scholar'],
        ['min' => 80, 'max' => null, 'title' => 'Quantum Sage'],
    ],
];
