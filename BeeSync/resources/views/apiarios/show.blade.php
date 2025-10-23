<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center bg-black p-4 rounded-md">
            <div>
                <h2 class="font-semibold text-xl text-white leading-tight">
                    Apiário: {{ $apiario->nome }}
                </h2>
                <hr class="bg-white">
                <p class="text-gray-200">{{ $apiario->localizacao }}</p>
            </div>

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-800 active:bg-yellow-900 focus:outline-none">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @can('is-owner-of-apiary', $apiario)
                        <a href="{{ route('colmeias.create', ['apiario' => $apiario]) }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Nova Colmeia
                        </a>
                    @endcan
                    @can('is-owner-of-apiary', $apiario)
                        <hr class="my-6">
                        <h3 class="text-lg font-semibold">Convidar Membro</h3>
                        <p class="text-sm text-gray-600">Convide outro usuário para realizar inspeções neste apiário.</p>
                        <form method="POST" action="{{ route('apiarios.invite', $apiario) }}" class="mt-4 flex">
                            @csrf
                            <input type="email" name="email" placeholder="E-mail do usuário"
                                class="block w-full rounded-md border-gray-300 shadow-sm" required>
                            <button type="submit"
                                class="ml-4 px-4 py-2 bg-gray-800 text-white rounded-md text-xs uppercase hover:bg-gray-700">
                                Convidar
                            </button>
                        </form>
                    @endcan
                    @can('is-owner-of-apiary', $apiario)
                        <div class="mt-6">
                            <h4 class="font-semibold">Membros Atuais</h4>
                            <ul class="mt-2 space-y-2">
                                @forelse ($apiario->membros as $membro)
                                    <li class="flex justify-between items-center p-2 bg-gray-50 rounded-md">
                                        <span>{{ $membro->name }} ({{ $membro->email }})</span>
                                        <form method="POST"
                                            action="{{ route('apiarios.member.destroy', [$apiario, $membro]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-sm text-red-600 hover:underline">Remover</button>
                                        </form>
                                    </li>
                                @empty
                                    <li class="text-sm text-gray-500">Nenhum membro convidado.</li>
                                @endforelse
                            </ul>
                        </div>
                    @endcan
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold">Colmeias neste Apiário</h3>
                        <ul class="mt-4 space-y-2">
                            @forelse ($apiario->colmeias as $colmeia)
                                <li class="p-4 bg-yellow-100 rounded-lg">
                                    <a href="{{ route('colmeias.show', $colmeia) }}"
                                        class="font-bold text-black hover:underline">
                                        {{ $colmeia->identificacao }}
                                    </a>
                                </li>
                            @empty
                                <p>Você ainda não cadastrou nenhuma colmeia.</p>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
