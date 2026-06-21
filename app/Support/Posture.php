<?php

namespace App\Support;

use App\Models\AuditFinding;
use App\Models\ClauseRequirement;
use App\Models\Control;
use App\Models\DocumentedInformation;
use App\Models\PostureSnapshot;
use App\Models\Risk;
use App\Models\SoaEntry;
use App\Models\Task;
use App\Models\TrainingRecord;
use Illuminate\Support\Carbon;

/** Headline ISMS posture computation + daily snapshot capture (ported from the FastAPI compute_headline). */
class Posture
{
    public static function headline(): array
    {
        $totalControls = Control::count();
        $implemented = Control::where('status', 'Implemented')->count();

        $totalApplicable = SoaEntry::where('applicable', true)->count();
        $fully = SoaEntry::where('applicable', true)->where('implementation_status', 'Fully')->count();
        $compliance = $totalApplicable
            ? round($fully / $totalApplicable * 100, 1)
            : ($totalControls ? round($implemented / $totalControls * 100, 1) : 0.0);

        $totalClauses = ClauseRequirement::count();
        $conformant = ClauseRequirement::where('conformity_status', 'Conformant')->count();
        $conformity = $totalClauses ? round($conformant / $totalClauses * 100, 1) : 0.0;

        $mandatory = DocumentedInformation::where('mandatory', true)->count();
        $approved = DocumentedInformation::where('mandatory', true)->where('status', 'Approved')->count();
        $readiness = $mandatory ? round($approved / $mandatory * 100, 1) : 0.0;

        $totalRecords = TrainingRecord::count();
        $completedRecords = TrainingRecord::where('status', 'Completed')->count();
        $training = $totalRecords ? round($completedRecords / $totalRecords * 100, 1) : 0.0;

        $openRisks = Risk::whereIn('status', ['Open', 'In Treatment'])->count();
        $criticalRisks = Risk::where('inherent_risk_level', 'Critical')->count();
        $openFindings = AuditFinding::whereIn('status', ['Open', 'In Progress'])->count();

        $tasks = Task::get(['due_date', 'status']);
        $openTasks = $tasks->whereIn('status', Task::OPEN_STATUSES)->count();
        $overdueTasks = $tasks->filter(fn ($t) => Scoring::taskIsOverdue($t->due_date, $t->status))->count();

        return [
            'compliance' => $compliance,
            'conformity' => $conformity,
            'readiness' => $readiness,
            'training' => $training,
            'implemented' => $implemented,
            'total_controls' => $totalControls,
            'open_risks' => $openRisks,
            'critical_risks' => $criticalRisks,
            'open_findings' => $openFindings,
            'open_tasks' => $openTasks,
            'overdue_tasks' => $overdueTasks,
        ];
    }

    public static function recordSnapshot(): void
    {
        $h = self::headline();
        PostureSnapshot::updateOrCreate(
            ['snapshot_date' => Carbon::today()->toDateString()],
            [
                'compliance_score' => $h['compliance'],
                'isms_conformity_score' => $h['conformity'],
                'document_readiness_score' => $h['readiness'],
                'training_completion_rate' => $h['training'],
                'implemented_controls' => $h['implemented'],
                'total_controls' => $h['total_controls'],
                'open_risks' => $h['open_risks'],
                'critical_risks' => $h['critical_risks'],
                'open_findings' => $h['open_findings'],
                'open_tasks' => $h['open_tasks'],
                'overdue_tasks' => $h['overdue_tasks'],
            ]
        );
    }
}
