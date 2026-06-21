<?php

namespace Tests\Unit;

use App\Support\Scoring;
use PHPUnit\Framework\TestCase;

/** Mirrors the FastAPI unit tests for the ported scoring helpers. */
class ScoringTest extends TestCase
{
    public function test_risk_level(): void
    {
        $this->assertSame('Low', Scoring::riskLevel(1, 1));      // 1
        $this->assertSame('Medium', Scoring::riskLevel(2, 3));   // 6
        $this->assertSame('High', Scoring::riskLevel(3, 4));     // 12
        $this->assertSame('Critical', Scoring::riskLevel(5, 4)); // 20
    }

    public function test_compute_rag_higher_is_better(): void
    {
        $this->assertSame('No Data', Scoring::computeRag(null, 5.0));
        $this->assertSame('On Target', Scoring::computeRag(100, 100, 'higher_is_better'));
        $this->assertSame('Near Target', Scoring::computeRag(100, 95, 'higher_is_better'));
        $this->assertSame('Off Target', Scoring::computeRag(100, 80, 'higher_is_better'));
    }

    public function test_compute_rag_lower_is_better(): void
    {
        $this->assertSame('On Target', Scoring::computeRag(5, 4, 'lower_is_better'));
        $this->assertSame('Near Target', Scoring::computeRag(5, 5.4, 'lower_is_better'));
        $this->assertSame('Off Target', Scoring::computeRag(5, 7, 'lower_is_better'));
    }

    public function test_aggregate_score_maturity_weighted(): void
    {
        $this->assertSame(60.0, Scoring::aggregateScore([3, 3], [null, null])); // 3/5 = 60%
        $this->assertSame(0.0, Scoring::aggregateScore([], []));
    }

    public function test_aggregate_score_from_results(): void
    {
        // (1 + 1 + 0.5) / 3 = 83.3%
        $this->assertSame(83.3, Scoring::aggregateScore([null, null, null], ['Compliant', 'Compliant', 'Partial']));
        $this->assertSame(100.0, Scoring::aggregateScore([], ['Yes', 'Compliant']));
    }

    public function test_task_is_overdue(): void
    {
        $this->assertTrue(Scoring::taskIsOverdue('2020-01-01', 'Open'));
        $this->assertFalse(Scoring::taskIsOverdue('2020-01-01', 'Done'));
        $this->assertFalse(Scoring::taskIsOverdue(null, 'Open'));
        $this->assertFalse(Scoring::taskIsOverdue('2999-01-01', 'Open'));
    }
}
