<?php

namespace App\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

/**
 * Pure scoring/derivation helpers ported 1:1 from the FastAPI models so the LAMP
 * edition computes identical numbers. Unit-tested in tests/Unit/ScoringTest.php.
 */
class Scoring
{
    /** Risk level from a 5x5 likelihood x impact matrix (score = l * i). */
    public static function riskLevel(?int $likelihood, ?int $impact): string
    {
        $score = (int) ($likelihood ?? 0) * (int) ($impact ?? 0);
        return match (true) {
            $score >= 20 => 'Critical',
            $score >= 12 => 'High',
            $score >= 6  => 'Medium',
            default      => 'Low',
        };
    }

    /** RAG status for a KPI/KRI/KCI given target, current and direction. */
    public static function computeRag(?float $target, ?float $current, string $direction = 'higher_is_better'): string
    {
        if ($target === null || $current === null) {
            return 'No Data';
        }
        if ($direction === 'lower_is_better') {
            if ($current <= $target) return 'On Target';
            if ($current <= $target * 1.1) return 'Near Target';
            return 'Off Target';
        }
        if ($current >= $target) return 'On Target';
        if ($current >= $target * 0.9) return 'Near Target';
        return 'Off Target';
    }

    /**
     * Assessment aggregate score (0..100).
     * Maturity-weighted when any maturity (0-5) is set; otherwise from results.
     *
     * @param array<int|null> $maturities
     * @param array<string|null> $results
     */
    public static function aggregateScore(array $maturities, array $results): float
    {
        $goodResults = ['Compliant', 'Yes'];
        $ratedResults = ['Compliant', 'Partial', 'Non-Compliant', 'Yes', 'No'];

        $mats = array_values(array_filter($maturities, fn ($m) => $m !== null));
        if (count($mats) > 0) {
            return round(array_sum($mats) / count($mats) / 5 * 100, 1);
        }

        $rated = array_values(array_filter($results, fn ($r) => in_array($r, $ratedResults, true)));
        if (count($rated) > 0) {
            $score = 0.0;
            foreach ($rated as $r) {
                if (in_array($r, $goodResults, true)) {
                    $score += 1;
                } elseif ($r === 'Partial') {
                    $score += 0.5;
                }
            }
            return round($score / count($rated) * 100, 1);
        }

        return 0.0;
    }

    /** Whether an open task with a due date is past due. */
    public static function taskIsOverdue($dueDate, ?string $status, $today = null): bool
    {
        $openStatuses = ['Open', 'In Progress', 'Blocked'];
        if (! $dueDate || ! in_array($status, $openStatuses, true)) {
            return false;
        }
        $today = $today ? Carbon::parse($today) : Carbon::today();
        return Carbon::parse($dueDate)->lt($today);
    }
}
