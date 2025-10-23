<?php

namespace App\Http\Controllers;

use App\Models\Apiario;
use App\Models\Colmeia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ColmeiaController extends Controller
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
    public function create(Apiario $apiario)
    {
        Gate::authorize('is-owner-of-apiary', $apiario);

        return view('colmeias.create', ['apiario' => $apiario]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'identificacao' => 'required|string|max:255',
        ]);

        $apiario = Apiario::findOrFail($request->apiario_id);
        Gate::authorize('is-owner-of-apiary', $apiario);

        $apiario->colmeias()->create([
            'identificacao' => $request->identificacao,
        ]);

        return redirect()->route('apiarios.show', $apiario)->with('succcess', 'Colmeia criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Colmeia $colmeia)
    {
        Gate::authorize('can-access-apiary', $colmeia->apiario);

        $colmeia->load(['inspecoes' => function ($query) {
            $query->with('inspetor')->orderBy('data_inspecao', 'desc');
        }]);

        return view('colmeias.show', ['colmeia' => $colmeia]);
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
