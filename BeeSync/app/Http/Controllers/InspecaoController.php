<?php

namespace App\Http\Controllers;

use App\Models\Colmeia;
use App\Models\Inspecao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class InspecaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Colmeia $colmeia)
    {
        $apiario = $colmeia->apiario;

        if (Gate::denies('can-access-apiary', $apiario)) {
            abort(403, 'Acesso não autorizado.');
        }

        return view('inspecoes.create', ['colmeia' => $colmeia]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'data_inspecao' => 'required|date',
            'viu_rainha' => 'required|boolean',
            'nivel_populacao' => 'required|integer|min:1|max:5',
            'reservas_mel' => 'required|integer|min:1|max:5',
            'sinais_parasitas' => 'required|boolean',
            'observacoes' => 'nullable|string',
            'colmeia_id' => 'required|exists:colmeias,id',
        ]);

        $colmeia = Colmeia::findOrFail($request->colmeia_id);
        Gate::authorize('can-access-apiary', $colmeia->apiario);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        $colmeia->inspecoes()->create($data);

        return redirect()->route('colmeias.show', $colmeia)->with('success', 'Inspeção registrada!');
    }

    public function sync(Request $request)
    {
        // Valida os dados que vieram do JSON
        $data = $request->validate([
            'colmeia_id' => 'required|exists:colmeias,id', //
            'data_inspecao' => 'required|date', //
            'viu_rainha' => 'required|boolean',
            'nivel_populacao' => 'required|integer|min:1|max:5',
            'reservas_mel' => 'required|integer|min:1|max:5',
            'sinais_parasitas' => 'required|boolean',
            'observacoes' => 'nullable|string',
            'offline_id' => 'required|string' // O ID que o JS gerou
        ]);

        // Encontre a colmeia e verifique a permissão
        $colmeia = Colmeia::findOrFail($data['colmeia_id']);
        Gate::authorize('can-access-apiary', $colmeia->apiario);

        // Adiciona o ID do inspetor (o usuário logado)
        $data['user_id'] = Auth::id();

        // Cria a inspeção
        Inspecao::create($data); //

        // Retorna sucesso, para que o JS possa limpar o item da fila
        return response()->json([
            'message' => 'Inspeção sincronizada com sucesso',
            'offline_id' => $data['offline_id']
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
