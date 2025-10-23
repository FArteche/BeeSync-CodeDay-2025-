<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center bg-black p-4 rounded-md">
            <div>
                <h2 class="font-semibold text-xl text-white leading-tight">
                    Colmeia: {{ $colmeia->identificacao }}
                </h2>
                <span class="text-sm text-gray-400">Do Apiário: {{ $colmeia->apiario->nome }}</span>
            </div>
            <a href="{{ route('apiarios.show', $colmeia->apiario->id) }}"
                class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-800 active:bg-yellow-900 focus:outline-none">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <a href="{{ route('inspecoes.create', ['colmeia' => $colmeia->id]) }}"
                        class="inline-flex items-center px-4 py-2 bg-black border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                        Registrar Nova Inspeção
                    </a>

                    <div class="mt-6">
                        <h3 class="text-lg font-semibold">Histórico de Inspeções</h3>
                        <ul class="mt-4 space-y-4">
                            @forelse ($colmeia->inspecoes as $inspecao)
                                <li class="p-4 bg-gray-50 rounded-lg border">
                                    <p class="font-bold">Data:
                                        {{ \Carbon\Carbon::parse($inspecao->data_inspecao)->format('d/m/Y') }}</p>
                                    <ul class="list-disc list-inside mt-2 text-sm">
                                        <li>Rainha Vista: {{ $inspecao->viu_rainha ? 'Sim' : 'Não' }}</li>
                                        <li>População: {{ $inspecao->nivel_populacao }} / 5</li>
                                        <li>Reservas de Mel: {{ $inspecao->reservas_mel }} / 5</li>
                                        <li>Sinais de Pragas: {{ $inspecao->sinais_parasitas ? 'Sim' : 'Não' }}</li>
                                        <li>Notas: {{ $inspecao->observacoes ?: 'Nenhuma nota escrita' }}</li>
                                        <li>Inspetor: {{ $inspecao->inspetor ? $inspecao->inspetor->name : 'Desconhecido' }}</li>
                                    </ul>
                                </li>
                            @empty
                                <p>Nenhuma inspeção registrada para esta colmeia.</p>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
