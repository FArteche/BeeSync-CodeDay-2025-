<?php

namespace App\Http\Controllers;

use App\Models\Apiario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ApiarioController extends Controller
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
    public function create()
    {
        return view('apiarios.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'localizacao' => 'required|string|max:255',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->apiarios()->create([
            'nome' => $request->nome,
            'localizacao' => $request->localizacao,
        ]);

        return redirect()->route('dashboard')->with('success', 'Apiário criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Apiario $apiario)
    {
        Gate::authorize('can-access-apiary', $apiario);

        $apiario->load('colmeias', 'membros');

        return view('apiarios.show', ['apiario' => $apiario]);
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
