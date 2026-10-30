<?php

namespace App\Services;

use App\Models\GraduationCompletion;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class GraduationAnalyticsService
{
    /**
     * Daily counts of completed graduation certificates within an inclusive date range.
     *
     * @return array{labels: list<string>, counts: list<int>, total: int, from: string, to: string}
     */
    public function completionsByDay(Carbon $from, Carbon $to): array
    {
        $fromDay = $from->copy()->startOfDay();
        $toDay = $to->copy()->startOfDay();

        if ($fromDay->gt($toDay)) {
            [$fromDay, $toDay] = [$toDay, $fromDay];
        }

        $rangeEnd = $to->copy()->endOfDay();

        $countsByDay = GraduationCompletion::query()
            ->whereBetween('graduated_at', [$fromDay, $rangeEnd])
            ->orderBy('graduated_at')
            ->get(['graduated_at'])
            ->groupBy(fn (GraduationCompletion $row) => $row->graduated_at->format('Y-m-d'))
            ->map(fn ($group) => $group->count())
            ->all();

        $labels = [];
        $counts = [];

        foreach (CarbonPeriod::create($fromDay, '1 day', $toDay) as $day) {
            $key = $day->format('Y-m-d');
            $labels[] = $key;
            $counts[] = (int) ($countsByDay[$key] ?? 0);
        }

        return [
            'labels' => $labels,
            'counts' => $counts,
            'total' => array_sum($counts),
            'from' => $fromDay->toDateString(),
            'to' => $toDay->toDateString(),
        ];
    }
}
