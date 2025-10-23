<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Apiario;
use App\Models\User;

class ApiarioMembroController extends Controller
{
    public function store(Request $request, Apiario $apiario)
    {
        Gate::authorize('is-owner-of-apiary', $apiario);

        $request->validate(['email' => 'required|email']);

        $usuarioConvidado = User::where('email', $request->email)->first();
        if (!$usuarioConvidado) {
            return back()->with('error', 'Usuário não encontrado.');
        }

        if ($apiario->user_id === $usuarioConvidado->id) {
            return back()->with('error', 'Este usuário já é o dono.');
        }

        if ($apiario->membros()->where('user_id', $usuarioConvidado->id)->exists()) {
            return back()->with('error', 'Este usuário já é um membro.');
        }

        $apiario->membros()->attach($usuarioConvidado->id);

        return back()->with('success', 'Usuário convidado com sucesso!');
    }

    public function destroy(Apiario $apiario, User $user)
    {
        Gate::authorize('is-owner-of-apiary', $apiario);

        $apiario->membros()->detach($user->id);

        return back()->with('success', 'Membro removido com sucesso.');
    }
}
