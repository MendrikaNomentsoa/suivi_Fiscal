<?php

use App\Http\Controllers\Api\AgentController;
use App\Http\Controllers\Api\ContribuableController;
use App\Http\Controllers\Api\DeclarationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EcheanceController;
use App\Http\Controllers\Api\LitigeController;
use App\Http\Controllers\Api\TraiterController;
use App\Http\Controllers\Api\TypeImpotController;

Route::apiResource('contribuables', ContribuableController::class);
Route::get('/contribuables/nif/{nif}', [ContribuableController::class, 'showByNif']);
// Retourne tous les impôts avec statut "fait" ou "non fait" pour un contribuable
Route::get('/contribuables/{id}/impots', [DeclarationController::class, 'impotsByContribuable']);
Route::get('impots/{id_contribuable}', [DeclarationController::class, 'impotsByContribuable']);


Route::apiResource('echeances', EcheanceController::class);

Route::apiResource('agents', AgentController::class);

Route::apiResource('litiges', LitigeController::class);

Route::apiResource('declarations', DeclarationController::class);

Route::get('/type-impots', [TypeImpotController::class, 'index']);



Route::get('litiges/en-attente', [TraiterController::class, 'index']);
Route::post('litiges/assigner', [TraiterController::class, 'assignerLitige']);
Route::delete('litiges/retirer', [TraiterController::class, 'destroy']);




