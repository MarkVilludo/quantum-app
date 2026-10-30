<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Services\GraduationAnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request, GraduationAnalyticsService $analytics): Response
    {
        $user = $request->user();
        $canViewAnalytics = in_array($user->role, ['admin', 'teacher'], true);

        $graduationAnalytics = null;
        $analyticsFilters = null;

        if ($canViewAnalytics) {
            $to = Carbon::parse($request->input('to', now()->toDateString()))->startOfDay();
            $from = Carbon::parse($request->input('from', now()->subDays(29)->toDateString()))->startOfDay();

            $graduationAnalytics = $analytics->completionsByDay($from, $to);
            $analyticsFilters = [
                'from' => $graduationAnalytics['from'],
                'to' => $graduationAnalytics['to'],
            ];
        }

        return Inertia::render('Dashboard', [
            'graduationAnalytics' => $graduationAnalytics,
            'analyticsFilters' => $analyticsFilters,
        ]);
    }
}
