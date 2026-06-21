<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClauseController;
use App\Http\Controllers\ControlController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RiskController;
use App\Http\Controllers\SoaController;
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
});
