<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AgentController;
use App\Http\Controllers\Api\ContribuableController;
use App\Http\Controllers\Api\DeclarationController;
use App\Http\Controllers\Api\EcheanceController;
use App\Http\Controllers\Api\LitigeController;
use App\Http\Controllers\Api\TraiterController;
use App\Http\Controllers\Api\TypeImpotController;
use App\Http\Controllers\Api\AuthController;


// ---------------------- Contribuables ----------------------
Route::apiResource('contribuables', ContribuableController::class);
Route::get('/contribuables/nif/{nif}', [ContribuableController::class, 'showByNif']);

// Retourne tous les impôts avec statut "fait / non fait" pour un contribuable
Route::get('/contribuables/{id}/impots', [DeclarationController::class, 'impotsByContribuable']);
Route::get('/impots/{id_contribuable}', [DeclarationController::class, 'impotsByContribuable']);

// ---------------------- Déclarations ----------------------
Route::get('/declarations', [DeclarationController::class, 'index']);
Route::post('/declarations', [DeclarationController::class, 'store']);
Route::get('/declarations/{id}', [DeclarationController::class, 'show']);
Route::put('/declarations/{id}', [DeclarationController::class, 'update']);
Route::delete('/declarations/{id}', [DeclarationController::class, 'destroy']);

// ---------------------- Échéances ----------------------
Route::apiResource('echeances', EcheanceController::class);

// ---------------------- Agents ----------------------
Route::apiResource('agents', AgentController::class);

// ---------------------- Litiges ----------------------
Route::apiResource('litiges', LitigeController::class);
Route::get('litiges/en-attente', [TraiterController::class, 'index']);
Route::post('litiges/assigner', [TraiterController::class, 'assignerLitige']);
Route::delete('litiges/retirer', [TraiterController::class, 'destroy']);

// ---------------------- Types d'impôt ----------------------
Route::get('/type-impots', [TypeImpotController::class, 'index']);



Route::post('/login', [AuthController::class, 'login']);


// Routes contribuable
Route::middleware(['auth.contribuable'])->group(function () {
    Route::get('/contribuable/dashboard', [ContribuableController::class, 'dashboard']);
    Route::get('/contribuable/declarations', [DeclarationController::class, 'indexContribuable']);
});

// Routes agent
Route::middleware(['auth.agent'])->group(function () {
    Route::get('/agent/dashboard', [AgentController::class, 'dashboard']);
    Route::get('/agent/contribuables', [AgentController::class, 'listeContribuables']);
    Route::get('/agent/litiges', [AgentController::class, 'listeLitiges']);
});




// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

// Contribuable
Route::middleware('checkrole:contribuable')->group(function () {
    Route::get('/contribuable/dashboard', [ContribuableController::class, 'dashboard']);
    Route::get('/contribuable/declarations', [ContribuableController::class, 'declarations']);
    Route::get('/contribuable/simulation', [ContribuableController::class, 'simulation']);
    Route::post('/contribuable/litiges', [ContribuableController::class, 'deposerLitige']);
});

// Agent
Route::middleware('checkrole:agent')->group(function () {
    Route::get('/agent/dashboard', [AgentController::class, 'dashboard']);
    Route::get('/agent/contribuables', [AgentController::class, 'listeContribuables']);
    Route::get('/agent/litiges', [AgentController::class, 'listeLitiges']);
});

Route::prefix('auth')->group(function () {
    Route::post('/contribuable/login', [AuthController::class, 'loginContribuable']);
    Route::post('/agent/login', [AuthController::class, 'loginAgent']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});



