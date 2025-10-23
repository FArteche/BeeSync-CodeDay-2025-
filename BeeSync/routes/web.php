<?php

use App\Http\Controllers\ApiarioController;
use App\Http\Controllers\ApiarioMembroController;
use App\Http\Controllers\ColmeiaController;
use App\Http\Controllers\InspecaoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardChartController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\OfflineDataController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Apiario;
use App\Models\User;
use App\Http\Controllers\OfflineCacheController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/offline', function () {
    return response()->file(public_path('offline.html'));
});

Route::get('/api/cache/apiario/{apiario}', [OfflineCacheController::class, 'getApiarioUrls'])
    ->middleware('auth')
    ->name('api.cache.apiario');

Route::get('/dashboard', function () {
    /** @var \App\Models\User $user */
    $user = Auth::user();

    $apiariosProprios = $user->apiarios()->get();
    $apiariosCompartilhados = $user->sharedApiarios()->get();

    $todasColmeias = $apiariosProprios->merge($apiariosCompartilhados)->load('colmeias')->pluck('colmeias')->flatten();

    return view('dashboard', [
        'apiariosProprios' => $apiariosProprios,
        'apiariosCompartilhados' => $apiariosCompartilhados,
        'todasColmeias' => $todasColmeias
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    //Rotas do perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Rota para mostrar o form de criação de colmeias dentro de um apiario
    Route::get('colmeias/create/{apiario}', [ColmeiaController::class, 'create'])->name('colmeias.create');

    //Rotas dos apiarios, colmeias e inspecoes (Resource)
    Route::resource('apiarios', ApiarioController::class);
    Route::resource('colmeias', ColmeiaController::class)->except(['create']);

    // Rota customizada para criar inspeção (deve vir antes do resource)
    Route::get('inspecoes/create/{colmeia}', [InspecaoController::class, 'create'])->name('inspecoes.create');
    Route::resource('inspecoes', InspecaoController::class)->except(['create']);

    //Rota para os gráficos
    Route::get('/api/chart-data', [DashboardChartController::class, 'getChartData'])
        ->name('api.chart.data');

    // Rota para dados offline
    Route::get('/api/data/offline', [OfflineDataController::class, 'getEssentialData'])
        ->name('api.data.offline');

    // Rotas de Relatórios
    Route::get('/relatorios', [ReportController::class, 'index'])->name('reports.index');

    //Rota para convidar membros para um apiario
    Route::post('apiarios/{apiario}/invite', [ApiarioMembroController::class, 'store'])->name('apiarios.invite');
    Route::delete('apiarios/{apiario}/membro/{user}', [ApiarioMembroController::class, 'destroy'])->name('apiarios.member.destroy');

    //Rota para sincronização das inspeções
    Route::post('/api/sync-inspecoes', [InspecaoController::class, 'sync'])->name('api.inspecoes.sync');
});

require __DIR__ . '/auth.php';
