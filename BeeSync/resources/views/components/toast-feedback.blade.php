@if (session('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
        class="fixed top-20 right-5 z-50 p-4 rounded-md shadow-lg bg-green-500 text-white">
        <strong>Sucesso!</strong> {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
        class="fixed top-20 right-5 z-50 p-4 rounded-md shadow-lg bg-red-500 text-white">
        <strong>Erro!</strong> {{ session('error') }}
    </div>
@endif
