<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Colmeia; // Importe o model Colmeia

class ReportController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $apiarioIds = $user->apiarios()->pluck('id');

        $allHives = Colmeia::query()
            ->whereIn('apiario_id', $apiarioIds)
            ->with('apiario')
            ->get();

        $apiaries = $user->apiarios;

        return view('reports.index', [
            'allHives' => $allHives,
            'apiaries' => $apiaries
        ]);
    }
}
