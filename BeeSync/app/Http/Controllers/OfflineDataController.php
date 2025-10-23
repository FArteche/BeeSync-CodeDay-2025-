<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfflineDataController extends Controller
{
    public function getEssentialData()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Busca apiários próprios e compartilhados
        $apiariosProprios = $user->apiarios()->with('colmeias')->get();
        $apiariosCompartilhados = $user->apiariosCompartilhados()->with('colmeias')->get();

        // Combina os apiários
        $apiarios = $apiariosProprios->merge($apiariosCompartilhados);

        // Formata os dados para cache offline
        $offlineData = [
            'apiarios' => $apiarios->map(function ($apiario) {
                return [
                    'id' => $apiario->id,
                    'nome' => $apiario->nome,
                    'localizacao' => $apiario->localizacao,
                    'user_id' => $apiario->user_id,
                    'colmeias' => $apiario->colmeias->map(function ($colmeia) {
                        return [
                            'id' => $colmeia->id,
                            'identificacao' => $colmeia->identificacao,
                            'apiario_id' => $colmeia->apiario_id,
                            'ultima_inspecao' => $colmeia->inspecoes()
                                ->orderBy('data_inspecao', 'desc')
                                ->first()
                        ];
                    })
                ];
            })
        ];

        return response()->json($offlineData);
    }
}
