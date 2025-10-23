<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center bg-black p-4 rounded-md">
            <h2 class="font-semibold text-xl text-white leading-tight">
                Nova Inspeção (Colmeia: {{ $colmeia->identificacao }})
            </h2>
            <a href="{{ route('colmeias.show', ['colmeia' => $colmeia]) }}"
                class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-800 active:bg-yellow-900 focus:outline-none">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="POST" action="{{ route('inspecoes.store') }}" id="form-inspecao">
                        @csrf

                        <input type="hidden" name="colmeia_id" value="{{ $colmeia->id }}">

                        <div class="mt-4">
                            <label for="data_inspecao" class="block font-medium text-sm text-gray-700">Data da
                                Inspeção</label>
                            <input id="data_inspecao" name="data_inspecao" type="date" value="{{ date('Y-m-d') }}"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                        </div>

                        <div class="mt-4">
                            <span class="block font-medium text-sm text-gray-700">Viu a Rainha?</span>
                            <div class="mt-2 space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="viu_rainha" value="1"
                                        class="rounded-full border-gray-300 text-indigo-600 shadow-sm" required>
                                    <span class="ml-2">Sim</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="viu_rainha" value="0"
                                        class="rounded-full border-gray-300 text-indigo-600 shadow-sm" required>
                                    <span class="ml-2">Não</span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="nivel_populacao" class="block font-medium text-sm text-gray-700">Nível de
                                População (1=Fraca, 5=Forte)</label>
                            <select id="nivel_populacao" name="nivel_populacao"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                                <option value="1">1 - Fraca</option>
                                <option value="2">2 - Ok</option>
                                <option value="3" selected>3 - Média</option>
                                <option value="4">4 - Boa</option>
                                <option value="5">5 - Forte</option>
                            </select>
                        </div>

                        <div class="mt-4">
                            <label for="reservas_mel" class="block font-medium text-sm text-gray-700">Reservas de Mel
                                (1=Baixa, 5=Alta)</label>
                            <select id="reservas_mel" name="reservas_mel"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                                <option value="1">1 - Baixa</option>
                                <option value="2">2 - Ok</option>
                                <option value="3" selected>3 - Média</option>
                                <option value="4">4 - Boa</option>
                                <option value="5">5 - Alta</option>
                            </select>
                        </div>

                        <div class="mt-4">
                            <span class="block font-medium text-sm text-gray-700">Viu sinais de Pragas (Ex:
                                Varroa)?</span>
                            <div class="mt-2 space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="sinais_parasitas" value="1"
                                        class="rounded-full border-gray-300 text-indigo-600 shadow-sm" required>
                                    <span class="ml-2">Sim</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="sinais_parasitas" value="0"
                                        class="rounded-full border-gray-300 text-indigo-600 shadow-sm" required>
                                    <span class="ml-2">Não</span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="observacoes" class="block font-medium text-sm text-gray-700">Notas
                                Adicionais</label>
                            <textarea id="observacoes" name="observacoes" rows="3"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300"></textarea>
                        </div>


                        <div class="flex items-center justify-end mt-4">
                            <button type="submit"
                                class="px-4 py-2 bg-black text-white rounded-md font-semibold text-xs uppercase hover:bg-gray-600">
                                Salvar Inspeção
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('form-inspecao').addEventListener('submit', function(event) {
            // 1. Pare o envio normal do formulário
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);

            // 2. Crie um objeto com os dados da inspeção
            const inspecaoData = {
                colmeia_id: formData.get('colmeia_id'), //
                data_inspecao: formData.get('data_inspecao'), //
                viu_rainha: formData.get('viu_rainha'),
                nivel_populacao: formData.get('nivel_populacao'),
                reservas_mel: formData.get('reservas_mel'),
                sinais_parasitas: formData.get('sinais_parasitas'),
                observacoes: formData.get('observacoes'),
                // Adiciona um ID único para este registro offline
                offline_id: Date.now().toString()
            };

            // 3. Verifique se está online
            if (navigator.onLine) {
                // Se ONLINE, envie para o servidor
                submitForm(inspecaoData, form.action);
            } else {
                // Se OFFLINE, salve localmente
                saveForLater(inspecaoData);
            }
        });

        // Função para salvar no localStorage
        function saveForLater(data) {
            // Pega a fila de inspeções pendentes (se existir)
            const queue = JSON.parse(localStorage.getItem('pending_inspections')) || [];
            queue.push(data); // Adiciona a nova inspeção na fila
            localStorage.setItem('pending_inspections', JSON.stringify(queue));

            // Dê um feedback para o usuário e redirecione
            alert('Você está offline. A inspeção foi salva localmente e será enviada assim que houver conexão.');
            // Redireciona para o dashboard ou para a pág. da colmeia
            window.location.href = "{{ route('colmeias.show', $colmeia) }}";
        }

        // Função para enviar os dados (usada quando online)
        async function submitForm(data, url) {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    // Se o formulário original redireciona, faça o mesmo
                    window.location.href = response.url;
                } else {
                    alert('Houve um erro ao salvar a inspeção.');
                }
            } catch (error) {
                console.error('Erro no submit:', error);
                alert('Houve um erro de rede. A inspeção será salva para envio posterior.');
                saveForLater(data); // Salva localmente se o envio falhar
            }
        }
    </script>
</x-app-layout>
