<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center bg-black p-4 rounded-md">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Relatórios e Métricas') }}
            </h2>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-800 active:bg-yellow-900 focus:outline-none">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900">

                    <details class="border rounded-lg" open>
                        <summary class="p-4 font-semibold cursor-pointer">
                            Filtros do Relatório
                        </summary>

                        <div class="p-4 border-t">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700">De</label>
                                    <input type="date" id="start_date"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label for="end_date" class="block text-sm font-medium text-gray-700">Até</label>
                                    <input type="date" id="end_date"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div class="col-span-1 md:col-span-2 space-x-2 flex flex-wrap gap-2">
                                    <button data-days="30"
                                        class="preset-date px-3 py-1 text-sm border rounded-md hover:bg-gray-100">30
                                        dias</button>
                                    <button data-days="90"
                                        class="preset-date px-3 py-1 text-sm border rounded-md hover:bg-gray-100">90
                                        dias</button>
                                    <button data-days="180"
                                        class="preset-date px-3 py-1 text-sm border rounded-md hover:bg-gray-100">6
                                        meses</button>
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <label for="mode_select" class="block text-sm font-medium text-gray-700">Modo de
                                            Visualização</label>
                                        <select id="mode_select"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                            <option value="average">Desempenho Médio</option>
                                            <option value="individual" selected>Comparar Colmeias</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="metrics_select" class="block text-sm font-medium text-gray-700">Métricas</label>
                                        <select id="metrics_select"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                            <option value="both" selected>População e Reservas de Mel</option>
                                            <option value="population">Apenas População</option>
                                            <option value="honey">Apenas Reservas de Mel</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-span-1 md:col-span-2 space-y-4">
                                    <!-- Seletor de Apiários -->
                                    <div>
                                        <label for="apiaries_select" class="block text-sm font-medium text-gray-700">Apiários</label>
                                        <select id="apiaries_select" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                            <option value="all">Todos os Apiários</option>
                                            @foreach($apiaries as $apiary)
                                                <option value="{{ $apiary->id }}">{{ $apiary->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Lista de Colmeias -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Colmeias</label>
                                        <div id="hive_checkbox_list" class="h-32 p-2 border rounded-md overflow-y-auto bg-gray-50">
                                            @forelse($allHives as $hive)
                                                <label class="flex items-center space-x-2 p-1 hover:bg-gray-100 rounded hive-item" data-apiary-id="{{ $hive->apiario->id }}">
                                                    <input type="checkbox" name="hive_ids[]" value="{{ $hive->id }}"
                                                        class="rounded border-gray-300 text-blue-600 shadow-sm">
                                                    <span>
                                                        {{ $hive->identificacao }} <span class="text-xs text-gray-500">
                                                            ({{ $hive->apiario->nome }})
                                                        </span>
                                                    </span>
                                                </label>
                                            @empty
                                                <p class="text-sm text-gray-500">Nenhuma colmeia encontrada.</p>
                                            @endforelse
                                        </div>
                                        <button id="select_all_hives" class="mt-2 text-sm text-blue-600 hover:underline">Selecionar Todas</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </details>

                    <div class="mt-4">
                        <button id="update_chart_button"
                            class="w-full sm:w-auto px-4 py-2 bg-black text-white rounded-md font-semibold text-xs uppercase hover:bg-gray-600">
                            Atualizar Gráfico
                        </button>
                        <span id="chart_loading" class="ml-4 text-sm text-gray-500 hidden">Carregando...</span>
                    </div>

                    <div class="mt-6">
                        <canvas id="myChart" data-chart-url="{{ route('api.chart.data') }}"></canvas>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js e adaptador Luxon -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/luxon@3.4.4/build/global/luxon.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@1.3.1/dist/chartjs-adapter-luxon.umd.min.js"></script>

    <script>
        // Configurar DateTime do Luxon para pt-BR
        luxon.Settings.defaultLocale = 'pt-BR';
        const DateTime = luxon.DateTime; // 3. Referências dos Elementos HTML
        const ctx = document.getElementById('myChart');
        const chartApiUrl = ctx.dataset.chartUrl;
        const btnUpdate = document.getElementById('update_chart_button');
        const btnPresets = document.querySelectorAll('.preset-date');
        const inputStartDate = document.getElementById('start_date');
        const inputEndDate = document.getElementById('end_date');
        const hiveCheckboxes = document.querySelectorAll('#hive_checkbox_list input[type="checkbox"]');
        const btnSelectAll = document.getElementById('select_all_hives');
        const selectMode = document.getElementById('mode_select');
        const selectMetrics = document.getElementById('metrics_select');
        const selectApiaries = document.getElementById('apiaries_select');
        const hiveItems = document.querySelectorAll('.hive-item');
        const loadingEl = document.getElementById('chart_loading');

        // 4. Inicializar o Gráfico (vazio)
        const myChart = new Chart(ctx, {
            type: 'line',
            data: {
                datasets: []
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'day',
                        },
                        title: {
                            display: true,
                            text: 'Data da Inspeção'
                        },
                        adapters: {
                            date: {
                                zone: 'America/Sao_Paulo'
                            }
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Nível (1-5)'
                        },
                        min: 1,
                        max: 5
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                return DateTime.fromMillis(context[0].parsed.x)
                                    .toLocaleString(DateTime.DATE_SHORT);
                            }
                        }
                    }
                }
            }
        });

        // 5. Função Principal: Buscar dados e atualizar o gráfico
        async function updateChart() {
            loadingEl.classList.remove('hidden');
            btnUpdate.disabled = true;

            const startDate = inputStartDate.value;
            const endDate = inputEndDate.value;
            const mode = selectMode.value;
            // Converter a seleção de métricas em um array
            let metrics;
            switch(selectMetrics.value) {
                case 'both':
                    metrics = ['population', 'honey'];
                    break;
                case 'population':
                    metrics = ['population'];
                    break;
                case 'honey':
                    metrics = ['honey'];
                    break;
            }

            const selectedHives = Array.from(hiveCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);

            if (!startDate || !endDate || selectedHives.length === 0) {
                alert('Por favor, preencha as datas e selecione pelo menos uma colmeia.');
                loadingEl.classList.add('hidden');
                btnUpdate.disabled = false;
                return;
            }

            // Construir URL absoluta
            const url = new URL(chartApiUrl, window.location.origin);
            url.searchParams.append('start_date', startDate);
            url.searchParams.append('end_date', endDate);
            url.searchParams.append('mode', mode);
            // Adicionar cada métrica como um item do array
            metrics.forEach(metric => {
                url.searchParams.append('metrics[]', metric);
            });
            selectedHives.forEach(id => {
                url.searchParams.append('hive_ids[]', id);
            });

            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                if (!response.ok) {
                    throw new Error('Erro ao buscar dados. ' + await response.text());
                }
                const data = await response.json();
                if (data.error) {
                    throw new Error(data.error);
                }
                myChart.data.datasets = data.datasets;
                myChart.update();

            } catch (error) {
                console.error('Erro detalhado:', error);
                alert('Erro ao carregar o gráfico: ' + error.message);
            } finally {
                loadingEl.classList.add('hidden');
                btnUpdate.disabled = false;
            }
        }

        // 6. Event Listeners (Gatilhos)
        btnUpdate.addEventListener('click', updateChart);

        btnPresets.forEach(button => {
            button.addEventListener('click', () => {
                const days = parseInt(button.dataset.days, 10);
                const end = new Date();
                const start = new Date();
                start.setDate(end.getDate() - days);

                inputEndDate.value = end.toISOString().split('T')[0];
                inputStartDate.value = start.toISOString().split('T')[0];

                let anyChecked = false;
                hiveCheckboxes.forEach(cb => {
                    cb.checked = true;
                    anyChecked = true;
                });

                if (anyChecked) {
                    btnSelectAll.textContent = 'Desselecionar Todas';
                }
            });
        });

        btnSelectAll.addEventListener('click', (e) => {
            e.preventDefault();
            const allChecked = Array.from(hiveCheckboxes).every(cb => cb.checked);
            hiveCheckboxes.forEach(cb => {
                cb.checked = !allChecked;
            });
            btnSelectAll.textContent = allChecked ? 'Selecionar Todas' : 'Desselecionar Todas';
        });

        // Função para filtrar colmeias por apiário
        function filterHivesByApiary() {
            const selectedApiaryId = selectApiaries.value;

            hiveItems.forEach(item => {
                if (selectedApiaryId === 'all' || item.dataset.apiaryId === selectedApiaryId) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                    // Desmarcar checkbox se a colmeia estiver oculta
                    item.querySelector('input[type="checkbox"]').checked = false;
                }
            });
        }

        // Event listener para o seletor de apiários
        selectApiaries.addEventListener('change', filterHivesByApiary);

        // 7. Configuração Inicial - apenas define as datas
        document.querySelector('.preset-date[data-days="30"]').click();
        // Prevenir a geração automática do gráfico
        hiveCheckboxes.forEach(cb => cb.checked = false);
        btnSelectAll.textContent = 'Selecionar Todas';
    </script>
</x-app-layout>
