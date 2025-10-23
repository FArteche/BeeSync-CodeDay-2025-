<?php

namespace App\Http\Controllers;

use App\Models\Apiario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class OfflineCacheController extends Controller
{
    public function getApiarioUrls(Request $request, Apiario $apiario)
    {
        // Verifica se o usuário tem acesso ao apiário
        $user = Auth::user();
        if (!$user || Gate::denies('view', $apiario)) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        // Carrega as colmeias relacionadas
        $apiario->load('colmeias');

        // Cria array de URLs para cache
        $urls = [
            route('dashboard'),
            route('apiarios.show', $apiario),
        ];

        // Adiciona URLs das colmeias
        foreach ($apiario->colmeias as $colmeia) {
            $urls[] = route('colmeias.show', $colmeia);
            $urls[] = route('inspecoes.create', $colmeia);
        }

        // Busca os assets do manifest.json
        $manifestPath = public_path('build/manifest.json');
        $assets = [];

        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
            foreach ($manifest as $entry) {
                if (isset($entry['file'])) {
                    $assets[] = "/build/{$entry['file']}";
                }
                if (isset($entry['css'])) {
                    foreach ($entry['css'] as $cssFile) {
                        $assets[] = "/build/{$cssFile}";
                    }
                }
            }
        }

        // Adiciona outros assets estáticos
        $staticAssets = [
            '/images/SmallBee.png',
            '/images/NormalBee.png',
            '/images/imagebg.jpg',
            '/manifest.json',
        ];

        return response()->json([
            'urls' => $urls,
            'assets' => array_merge($assets, $staticAssets)
        ]);
    }
}
