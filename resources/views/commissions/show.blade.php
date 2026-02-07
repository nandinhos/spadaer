<x-app-layout>
    {{-- Header etc. --}}
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Detalhes da Comissão: {{ $commission->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
            {{-- Detalhes da Comissão --}}
            <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Informações</h3>
                    <dl class="mt-4 space-y-4 text-sm">
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Nome</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $commission->name }}</dd>
                        </div>
                        {{-- Adicione outros detalhes da comissão/portaria --}}
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Portaria</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $commission->ordinance }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Data da Portaria</dt>
                            <dd class="text-gray-900 dark:text-gray-100">
                                {{ $commission->ordinance_date->format('d/m/Y') }}</dd>
                        </div>
                        @if ($commission->ordinance_file)
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400">Arquivo</dt>
                                <dd>
                                    <a href="{{ Storage::url($commission->ordinance_file) }}" target="_blank"
                                        class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                        Visualizar PDF
                                    </a>
                                </dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Descrição</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $commission->description ?: '-' }}</dd>
                        </div>
                        {{-- Adicione o status aqui se tiver --}}

                    </dl>
                                        <div class="flex gap-4 mt-6">
                        <a href="{{ route('commissions.edit', $commission) }}" wire:navigate>
                            <x-ui.button variant="primary" icon="fas fa-edit">Editar</x-ui.button>
                        </a>
                        {{-- Botão de Excluir com formulário --}}
                        <form method="POST" action="{{ route('commissions.destroy', $commission) }}"
                            onsubmit="return confirm('Tem certeza que deseja excluir esta comissão?');">
                            @csrf
                            @method('DELETE')
                            <x-ui.button variant="danger" icon="fas fa-trash-alt" type="submit">Excluir</x-ui.button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Lista de Membros --}}
            <div class="p-4 bg-white shadow sm:p-8 dark:bg-gray-800 sm:rounded-lg">
                <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">Membros da Comissão</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                    Posto/Grad.</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                    Nome Completo</th>
                                {{-- <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Número de Ordem</th> --}}
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                    Função</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            {{-- Acessa a coleção de CommissionMember via $commission->members --}}
                            {{-- Certifique-se que o controller está carregando o relacionamento user: $commission->load('members.user') --}}
                            @forelse ($commission->members as $member)
                                {{-- $member é um CommissionMember --}}
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap dark:text-gray-100">
                                        {{-- Acessa o usuário através do relacionamento e depois o rank --}}
                                        {{ $member->user?->rank ?? 'N/D' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap dark:text-gray-100">
                                        {{-- Acessa o nome do usuário --}}
                                        {{ $member->user?->name ?? 'Usuário não encontrado' }}
                                    </td>
                                    {{-- <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap dark:text-gray-100">
                                         {{ $member->user?->order_number ?? '-' }}
                                     </td> --}}
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap dark:text-gray-100">
                                        {{-- Acessa a propriedade 'role' DIRETAMENTE do CommissionMember --}}
                                        {{ ucfirst($member->role) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    {{-- Ajustado colspan para 3 colunas visíveis --}}
                                    <td colspan="3"
                                        class="px-6 py-4 text-sm text-center text-gray-500 whitespace-nowrap dark:text-gray-400">
                                        Nenhum membro associado a esta comissão/portaria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex justify-start mt-6">
                <a href="{{ route('commissions.index') }}" wire:navigate>
                    <x-ui.button variant="secondary" icon="fas fa-arrow-left">
                        Voltar para Lista
                    </x-ui.button>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
