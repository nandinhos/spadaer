<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
<div class="flex items-center justify-between mb-2">
    <label for="csv_file" class="text-sm font-bold text-gray-700 dark:text-gray-300">
        Importar Documentos (CSV)
    </label>
    <a href="{{ asset('files/modelo_importacao.csv') }}" 
       class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
       download>
        Baixar modelo CSV
    </a>
</div>
<form action="{{ route('documents.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
           
            <div class="flex items-center space-x-4">
                <input type="file" 
                       id="csv_file" 
                       name="csv_file" 
                       accept=".csv"
                       class="block w-full text-sm text-gray-500 dark:text-gray-400
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-md file:border-0
                              file:text-sm file:font-semibold
                              file:bg-blue-50 file:text-blue-700
                              hover:file:bg-blue-100
                              dark:file:bg-gray-700 dark:file:text-gray-300"
                       required>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Importar
                </button>
            </div>
        </div>
        @if ($errors->any())
            <div class="text-red-600 dark:text-red-400 text-sm mt-2">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="text-green-600 dark:text-green-400 text-sm mt-2">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="text-red-600 dark:text-red-400 text-sm mt-2">
                {{ session('error') }}
            </div>
        @endif
    </form>
</div>