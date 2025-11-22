<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TypeImpot;
use Illuminate\Http\JsonResponse;

class TypeImpotController extends Controller
{
    public function index(): JsonResponse
    {
        // Récupère tous les types d'impôts
        $typeImpots = TypeImpot::all();

        return response()->json($typeImpots);
    }
}
