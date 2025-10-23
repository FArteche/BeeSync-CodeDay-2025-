<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Inspecao;
use App\Models\Colmeia;
use Carbon\Carbon;

class DashboardChartController extends Controller
{
    public function getChartData(Request $request)
    {
        try {
            Log::info('Iniciando getChartData', [
                'request' => $request->all(),
                'user_id' => Auth::id()
            ]);

            // 1. Validar os filtros
            $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'hive_ids'   => 'required|array',
            // Valida na tabela 'colmeias'
            'hive_ids.*' => 'integer|exists:colmeias,id',
            'mode'       => 'required|in:average,individual',
            'metrics'    => 'required|array',
            'metrics.*'  => 'required|in:population,honey',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $hiveIds = $request->hive_ids;
        $mode = $request->mode;
        $metrics = $request->metrics;

        // 2. Query Base (COM SEGURANÇA E NOMES CORRETOS)
        $query = Inspecao::query() // Tabela 'inspecaos'
            ->select(
                'inspecaos.data_inspecao', //
                'inspecaos.colmeia_id', //
                'inspecaos.nivel_populacao', //
                'inspecaos.reservas_mel' //
            )
            // Join com 'colmeias' usando 'colmeia_id'
            ->join('colmeias', 'inspecaos.colmeia_id', '=', 'colmeias.id')
            // Join com 'apiarios' usando 'apiario_id'
            ->join('apiarios', 'colmeias.apiario_id', '=', 'apiarios.id')
            // Filtro de segurança
            ->where('apiarios.user_id', Auth::id())
            ->whereIn('inspecaos.colmeia_id', $hiveIds) //
            // Filtro de data
            ->whereBetween('inspecaos.data_inspecao', [$startDate, $endDate])
            ->orderBy('inspecaos.data_inspecao', 'asc'); //

        $datasets = [];

        // 3. Processar os dados com base no MODO
        if ($mode === 'average') {
            // ----- MODO: MÉDIA -----
            $avgData = Inspecao::query()
                ->join('colmeias', 'inspecaos.colmeia_id', '=', 'colmeias.id')
                ->join('apiarios', 'colmeias.apiario_id', '=', 'apiarios.id')
                ->where('apiarios.user_id', Auth::id())
                ->whereIn('inspecaos.colmeia_id', $hiveIds)
                ->whereBetween('inspecaos.data_inspecao', [$startDate, $endDate])
                ->groupBy(DB::raw('DATE(data_inspecao)'))
                ->selectRaw('
                    DATE(data_inspecao) as date,
                    ROUND(AVG(nivel_populacao), 1) as avg_pop,
                    ROUND(AVG(reservas_mel), 1) as avg_honey
                ')
                ->orderBy('date', 'asc')
                ->get();

            if (in_array('population', $metrics)) {
                $datasets[] = [
                    'label' => 'População Média',
                    'data' => $avgData->map(function($item) {
                        return [
                            'x' => date('Y-m-d', strtotime($item->date)),
                            'y' => (float)$item->avg_pop
                        ];
                    })->toArray(),
                    'borderColor' => 'rgb(54, 162, 235)',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderWidth' => 2,
                    'tension' => 0.4,
                    'fill' => true
                ];
            }
            if (in_array('honey', $metrics)) {
                $datasets[] = [
                    'label' => 'Reservas de Mel (Média)',
                    'data' => $avgData->map(function($item) {
                        return [
                            'x' => date('Y-m-d', strtotime($item->date)),
                            'y' => (float)$item->avg_honey
                        ];
                    })->toArray(),
                    'borderColor' => 'rgb(255, 205, 86)',
                    'backgroundColor' => 'rgba(255, 205, 86, 0.2)',
                    'borderWidth' => 2,
                    'tension' => 0.4,
                    'fill' => true
                ];
            }
        } else {
            // ----- MODO: INDIVIDUAL -----
            $inspections = $query->get();
            // Pega 'identificacao' da tabela 'colmeias'
            $hiveNames = Colmeia::whereIn('id', $hiveIds)->pluck('identificacao', 'id');
            $groupedByHive = $inspections->groupBy('colmeia_id'); //

            foreach ($groupedByHive as $hiveId => $hiveInspections) {
                $hiveName = $hiveNames[$hiveId] ?? "Colmeia $hiveId";

                // Cores para os datasets
                $colors = [
                    'rgb(54, 162, 235)', // azul
                    'rgb(255, 99, 132)', // vermelho
                    'rgba(125, 243, 78, 1)', // verde
                    'rgba(0, 0, 0, 1)', // preto
                    'rgb(153, 102, 255)', // roxo
                    'rgb(255, 159, 64)'  // laranja
                ];
                $colorIndex = array_rand($colors);

                if (in_array('population', $metrics)) {
                    $datasets[] = [
                        'label' => "População - $hiveName",
                        'data' => $hiveInspections->map(function($i) {
                            return [
                                'x' => $i->data_inspecao instanceof \Carbon\Carbon
                                    ? $i->data_inspecao->format('Y-m-d')
                                    : date('Y-m-d', strtotime($i->data_inspecao)),
                                'y' => (float)$i->nivel_populacao
                            ];
                        })->toArray(),
                        'borderColor' => $colors[$colorIndex],
                        'backgroundColor' => $colors[$colorIndex],
                        'tension' => 0.1
                    ];
                }

                if (in_array('honey', $metrics)) {
                    $datasets[] = [
                        'label' => "Mel - $hiveName",
                        'data' => $hiveInspections->map(function($i) {
                            return [
                                'x' => $i->data_inspecao instanceof \Carbon\Carbon
                                    ? $i->data_inspecao->format('Y-m-d')
                                    : date('Y-m-d', strtotime($i->data_inspecao)),
                                'y' => (float)$i->reservas_mel
                            ];
                        })->toArray(),
                        'borderColor' => $colors[$colorIndex],
                        'backgroundColor' => $colors[$colorIndex],
                        'borderDash' => [5, 5],
                        'tension' => 0.1
                    ];
                }
            }
        }

            // 4. Log dos resultados
            Log::info('Dados processados com sucesso', [
                'datasets_count' => count($datasets),
                'first_dataset' => $datasets[0] ?? null
            ]);

            // 5. Retornar os datasets como JSON
            return response()->json(['datasets' => $datasets]);

        } catch (\Exception $e) {
            Log::error('Erro ao processar dados do gráfico', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Erro ao processar dados do gráfico: ' . $e->getMessage()
            ], 500);
        }
    }
}
