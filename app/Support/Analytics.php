<?php

namespace App\Support;

use App\Models\AuditFinding;
use App\Models\Control;
use App\Models\ControlMapping;
use App\Models\Risk;
use App\Models\Task;
use App\Models\User;

/** Risk heatmap, cross-framework coverage matrix and personal "My Work" rollups. */
class Analytics
{
    /** 5x5 likelihood x impact matrix with per-cell counts. */
    public static function heatmap(string $basis = 'inherent'): array
    {
        $risks = Risk::get(['likelihood', 'impact', 'residual_likelihood', 'residual_impact']);
        $counts = [];
        foreach ($risks as $r) {
            if ($basis === 'residual' && $r->residual_likelihood && $r->residual_impact) {
                $l = $r->residual_likelihood;
                $i = $r->residual_impact;
            } else {
                $l = $r->likelihood;
                $i = $r->impact;
            }
            $counts["$l-$i"] = ($counts["$l-$i"] ?? 0) + 1;
        }
        $cells = [];
        $total = 0;
        for ($l = 5; $l >= 1; $l--) {
            for ($i = 1; $i <= 5; $i++) {
                $c = $counts["$l-$i"] ?? 0;
                $total += $c;
                $cells[] = ['l' => $l, 'i' => $i, 'score' => $l * $i, 'level' => Scoring::riskLevel($l, $i), 'count' => $c];
            }
        }

        return ['basis' => $basis, 'cells' => $cells, 'total' => $total];
    }

    /** Cross-framework crosswalk coverage ("test once, comply many"). */
    public static function frameworkCoverage(): array
    {
        $controls = Control::get(['id', 'framework']);
        $fwOf = [];
        $totals = [];
        foreach ($controls as $c) {
            $fwOf[$c->id] = $c->framework;
            $totals[$c->framework] = ($totals[$c->framework] ?? 0) + 1;
        }

        $covered = [];
        foreach (ControlMapping::get(['source_control_id', 'target_control_id']) as $m) {
            $fa = $fwOf[$m->source_control_id] ?? null;
            $fb = $fwOf[$m->target_control_id] ?? null;
            if (! $fa || ! $fb || $fa === $fb) {
                continue;
            }
            $covered["$fa|$fb"][$m->source_control_id] = true;
            $covered["$fb|$fa"][$m->target_control_id] = true;
        }

        $fwList = array_keys($totals);
        sort($fwList);
        $matrix = [];
        foreach ($fwList as $src) {
            foreach ($fwList as $tgt) {
                if ($src === $tgt) {
                    continue;
                }
                $mapped = count($covered["$src|$tgt"] ?? []);
                $matrix["$src|$tgt"] = [
                    'mapped' => $mapped,
                    'total' => $totals[$src],
                    'pct' => $totals[$src] ? round($mapped / $totals[$src] * 100, 1) : 0.0,
                ];
            }
        }

        $anyCovered = [];
        foreach ($covered as $k => $ids) {
            [$src] = explode('|', $k);
            foreach ($ids as $id => $_) {
                $anyCovered[$src][$id] = true;
            }
        }
        $summary = [];
        foreach ($fwList as $fw) {
            $m = count($anyCovered[$fw] ?? []);
            $summary[] = [
                'framework' => $fw,
                'total' => $totals[$fw],
                'mapped_any' => $m,
                'pct' => $totals[$fw] ? round($m / $totals[$fw] * 100, 1) : 0.0,
            ];
        }

        return ['frameworks' => $summary, 'matrix' => $matrix, 'fwList' => $fwList];
    }

    public static function myWork(User $u): array
    {
        $tasks = Task::where('assignee_id', $u->id)->get(['due_date', 'status', 'task_type']);

        return [
            'open_tasks' => $tasks->whereIn('status', Task::OPEN_STATUSES)->count(),
            'overdue_tasks' => $tasks->filter(fn ($t) => Scoring::taskIsOverdue($t->due_date, $t->status))->count(),
            'pending_approvals' => $tasks->where('task_type', 'Approval')->whereIn('status', Task::OPEN_STATUSES)->count(),
            'owned_controls' => Control::where('owner_id', $u->id)->count(),
            'owned_risks' => Risk::where('owner_id', $u->id)->count(),
            'assigned_findings' => AuditFinding::where('assigned_to', $u->id)->count(),
        ];
    }
}
