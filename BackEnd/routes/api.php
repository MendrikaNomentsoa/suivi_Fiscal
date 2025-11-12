<?php

use App\Http\Controllers\Api\AgentController;
use App\Http\Controllers\Api\CategorieController;
use App\Http\Controllers\Api\ContribuableController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EcheanceController;
use App\Http\Controllers\Api\LitigeController;
use App\Http\Controllers\Api\TraiterController;

Route::apiResource('categories', CategorieController::class);

Route::apiResource('contribuables', ContribuableController::class);

Route::apiResource('echeances', EcheanceController::class);

Route::apiResource('agents', AgentController::class);

Route::apiResource('litiges', LitigeController::class);


Route::get('litiges/en-attente', [TraiterController::class, 'index']);
Route::post('litiges/assigner', [TraiterController::class, 'assignerLitige']);
Route::delete('litiges/retirer', [TraiterController::class, 'destroy']);




