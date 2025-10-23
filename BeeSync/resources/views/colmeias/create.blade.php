<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center bg-black p-4 rounded-md">
            <h2 class="font-semibold text-xl text-white leading-tight">
                Nova Colmeia (Apiário: {{ $apiario->nome }})
            </h2>
            <a href="{{ route('apiarios.show', ['apiario' => $apiario]) }}"
                class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-800 active:bg-yellow-900 focus:outline-none">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="POST" action="{{ route('colmeias.store') }}">
                        @csrf

                        <input type="hidden" name="apiario_id" value="{{ $apiario->id }}">

                        <div>
                            <label for="identificacao" class="block font-medium text-sm text-gray-700">Identificação da Colmeia</label>
                            <input id="identificacao" name="identificacao" type="text" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required autofocus>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md font-semibold text-xs uppercase hover:bg-gray-700">
                                Salvar Colmeia
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
