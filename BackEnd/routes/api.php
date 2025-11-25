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

// ========== ROUTES TYPES D'IMPÔTS ==========
Route::get('/types-impots', [TypeImpotController::class, 'index']);
Route::get('/types-impots/{id}', [TypeImpotController::class, 'show']);
Route::post('/types-impots', [TypeImpotController::class, 'store']);
Route::put('/types-impots/{id}', [TypeImpotController::class, 'update']);
Route::delete('/types-impots/{id}', [TypeImpotController::class, 'destroy']);

// ========== ROUTES DÉCLARATIONS ==========
Route::get('/declarations', [DeclarationController::class, 'index']);
Route::post('/declarations', [DeclarationController::class, 'store']);
Route::get('/declarations/{id}', [DeclarationController::class, 'show']);
Route::put('/declarations/{id}', [DeclarationController::class, 'update']);
Route::delete('/declarations/{id}', [DeclarationController::class, 'destroy']);

// Routes spécifiques déclarations
Route::get('/declarations/stats/{idContribuable}', [DeclarationController::class, 'statistiques']);

// ========== ROUTES CONTRIBUABLES ==========
Route::get('/impots/{idContribuable}', [DeclarationController::class, 'impotsByContribuable']);

// ---------------------- Échéances ----------------------
// Routes Échéances
Route::prefix('echeances')->group(function () {
    Route::get('/', [EcheanceController::class, 'index']);
    Route::get('/{id}', [EcheanceController::class, 'show']);
    Route::put('/{id}', [EcheanceController::class, 'update']);
    Route::delete('/{id}', [EcheanceController::class, 'destroy']);

    // Paiements
    Route::post('/{id}/paiement', [EcheanceController::class, 'enregistrerPaiement']);
    Route::post('/{id}/appliquer-penalites', [EcheanceController::class, 'appliquerPenalites']);

    // Filtres et statistiques
    Route::get('/contribuable/{id_contribuable}', [EcheanceController::class, 'parContribuable']);
    Route::get('/retards/liste', [EcheanceController::class, 'echeancesEnRetard']);
    Route::get('/statistiques/general', [EcheanceController::class, 'statistiques']);
});

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



