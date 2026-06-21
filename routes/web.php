<?php

use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClauseController;
use App\Http\Controllers\ControlController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\InterestedPartyController;
use App\Http\Controllers\MetricController;
use App\Http\Controllers\ObjectiveController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\RiskController;
use App\Http\Controllers\SoaController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TrainingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web routes — IT-GRC Portal (LAMP / Laravel edition)
|--------------------------------------------------------------------------
| The base layout's sidebar uses Route::has(), so the menu auto-populates as
| each module's routes come online.
*/

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ── Controls + cross-framework crosswalk ──────────────────────────────
    Route::get('controls', [ControlController::class, 'index'])->name('controls.index');
    Route::post('controls', [ControlController::class, 'store'])->name('controls.store')->middleware('permission:controls:write');
    Route::get('controls/{control}', [ControlController::class, 'show'])->name('controls.show');
    Route::put('controls/{control}', [ControlController::class, 'update'])->name('controls.update')->middleware('permission:controls:write');
    Route::delete('controls/{control}', [ControlController::class, 'destroy'])->name('controls.destroy')->middleware('permission:controls:write');
    Route::post('controls/{control}/mappings', [ControlController::class, 'addMapping'])->name('controls.mappings.store')->middleware('permission:controls:write');
    Route::delete('controls/{control}/mappings/{mapping}', [ControlController::class, 'deleteMapping'])->name('controls.mappings.destroy')->middleware('permission:controls:write');

    // ── Risk register (create before {risk} so it isn't captured as a param) ─
    Route::get('risks', [RiskController::class, 'index'])->name('risks.index');
    Route::get('risks/create', [RiskController::class, 'create'])->name('risks.create')->middleware('permission:risks:write');
    Route::post('risks', [RiskController::class, 'store'])->name('risks.store')->middleware('permission:risks:write');
    Route::get('risks/{risk}', [RiskController::class, 'show'])->name('risks.show');
    Route::get('risks/{risk}/edit', [RiskController::class, 'edit'])->name('risks.edit')->middleware('permission:risks:write');
    Route::put('risks/{risk}', [RiskController::class, 'update'])->name('risks.update')->middleware('permission:risks:write');
    Route::delete('risks/{risk}', [RiskController::class, 'destroy'])->name('risks.destroy')->middleware('permission:risks:write');
    Route::post('risks/{risk}/controls', [RiskController::class, 'linkControl'])->name('risks.controls.store')->middleware('permission:risks:write');
    Route::delete('risks/{risk}/controls/{control}', [RiskController::class, 'unlinkControl'])->name('risks.controls.destroy')->middleware('permission:risks:write');

    // ── ISMS clauses (4–10) ───────────────────────────────────────────────
    Route::get('clauses', [ClauseController::class, 'index'])->name('clauses.index');
    Route::get('clauses/{clause}', [ClauseController::class, 'show'])->name('clauses.show');
    Route::put('clauses/{clause}', [ClauseController::class, 'update'])->name('clauses.update');

    // ── Statement of Applicability ────────────────────────────────────────
    Route::get('soa', [SoaController::class, 'index'])->name('soa.index');
    Route::get('soa/{control}/edit', [SoaController::class, 'edit'])->name('soa.edit');
    Route::put('soa/{control}', [SoaController::class, 'update'])->name('soa.update')->middleware('permission:soa:write');

    // ── Register modules (resourceful CRUD) ───────────────────────────────
    Route::resource('documents', DocumentController::class)->except(['show']);
    Route::resource('suppliers', SupplierController::class)->except(['show']);
    Route::resource('incidents', IncidentController::class)->except(['show']);
    Route::resource('assets', AssetController::class)->except(['show']);
    Route::resource('objectives', ObjectiveController::class)->except(['show']);
    Route::resource('interested-parties', InterestedPartyController::class)
        ->parameters(['interested-parties' => 'interested_party'])->except(['show']);

    // ── Policies (+ acknowledge) ──────────────────────────────────────────
    Route::resource('policies', PolicyController::class);
    Route::post('policies/{policy}/acknowledge', [PolicyController::class, 'acknowledge'])->name('policies.acknowledge');

    // ── Metrics (+ measurements) ──────────────────────────────────────────
    Route::resource('metrics', MetricController::class);
    Route::post('metrics/{metric}/measurements', [MetricController::class, 'addMeasurement'])->name('metrics.measurements.store');

    // ── Workflow tasks (+ approval decision) ──────────────────────────────
    Route::resource('tasks', TaskController::class)->except(['show']);
    Route::post('tasks/{task}/decision', [TaskController::class, 'decision'])->name('tasks.decision');

    // ── Assessments (+ populate + items) ──────────────────────────────────
    Route::resource('assessments', AssessmentController::class);
    Route::post('assessments/{assessment}/populate', [AssessmentController::class, 'populate'])->name('assessments.populate');
    Route::post('assessments/{assessment}/items', [AssessmentController::class, 'storeItem'])->name('assessments.items.store');
    Route::put('assessments/{assessment}/items/{item}', [AssessmentController::class, 'updateItem'])->name('assessments.items.update');
    Route::delete('assessments/{assessment}/items/{item}', [AssessmentController::class, 'destroyItem'])->name('assessments.items.destroy');

    // ── Audits (+ findings) ───────────────────────────────────────────────
    Route::resource('audits', AuditController::class);
    Route::post('audits/{audit}/findings', [AuditController::class, 'storeFinding'])->name('audits.findings.store');
    Route::put('audits/{audit}/findings/{finding}', [AuditController::class, 'updateFinding'])->name('audits.findings.update');
    Route::delete('audits/{audit}/findings/{finding}', [AuditController::class, 'destroyFinding'])->name('audits.findings.destroy');

    // ── Awareness & training (+ records) ──────────────────────────────────
    Route::resource('training', TrainingController::class);
    Route::post('training/{training}/records', [TrainingController::class, 'storeRecord'])->name('training.records.store');
    Route::put('training/{training}/records/{record}', [TrainingController::class, 'updateRecord'])->name('training.records.update');
    Route::delete('training/{training}/records/{record}', [TrainingController::class, 'destroyRecord'])->name('training.records.destroy');

    // ── Evidence (file uploads) ───────────────────────────────────────────
    Route::get('evidence', [EvidenceController::class, 'index'])->name('evidence.index');
    Route::post('evidence', [EvidenceController::class, 'store'])->name('evidence.store');
    Route::get('evidence/{evidence}/download', [EvidenceController::class, 'download'])->name('evidence.download');
    Route::delete('evidence/{evidence}', [EvidenceController::class, 'destroy'])->name('evidence.destroy');
});
