{{-- Componente de Confirmacao de Acoes (Exclusao, etc) --}}
<div
    x-data="{
        get cd() {
            return Alpine.store('confirmDelete');
        }
    }"
    x-show="cd && cd.show"
    x-trap.noscroll="cd && cd.show"
    x-on:keydown.escape.window="cd && cd.show && cd.close()"
    class="fixed inset-0 z-[70] overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
    x-cloak
>
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:items-center sm:block sm:p-0">
        {{-- Background overlay --}}
        <div
            x-show="cd && cd.show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 transition-opacity bg-gray-900/75 backdrop-blur-sm"
            @click="cd?.close()"
            aria-hidden="true"
        ></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        {{-- Conteudo do Modal --}}
        <div
            x-show="cd && cd.show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block w-full overflow-hidden text-left align-bottom transition-all transform bg-white rounded-xl shadow-2xl dark:bg-gray-800 sm:my-8 sm:align-middle sm:max-w-lg relative z-10"
        >
            <div class="p-6">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 bg-red-100 rounded-full dark:bg-red-900/30">
                        <i class="text-red-600 fas fa-exclamation-triangle dark:text-red-500"></i>
                    </div>
                    <div class="text-left">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100" x-text="cd?.title"></h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400" x-text="cd?.message"></p>
                    </div>
                </div>

                <div x-show="cd?.requiresObservation" class="mt-4 px-1">
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-1.5 ml-1">
                        Motivo da Exclusão <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        x-model="cd.observation"
                        class="w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 focus:ring-primary focus:border-primary text-sm p-3 min-h-[100px]"
                        :class="cd.observationError ? 'border-red-500 ring-1 ring-red-500' : ''"
                        placeholder="Descreva o motivo desta exclusão..."
                        @input="cd.observationError = false"
                    ></textarea>
                    <p x-show="cd.observationError" class="mt-1 text-xs text-red-500 font-bold ml-1">
                        O motivo é obrigatório para prosseguir.
                    </p>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row-reverse gap-3">
                <form method="POST" class="w-full sm:w-auto" id="confirm-delete-form" x-bind:action="cd?.action">
                    @csrf
                    <input type="hidden" name="_method" x-bind:value="cd?.method">

                    <x-ui.button
                        type="button"
                        variant="danger"
                        class="w-full sm:w-auto"
                        x-on:click="cd?.handleConfirm()"
                        x-text="cd?.confirmText"
                    ></x-ui.button>
                </form>

                <x-ui.button
                    variant="secondary"
                    @click="cd?.close()"
                    class="w-full sm:w-auto"
                    x-text="cd?.cancelText"
                ></x-ui.button>
            </div>
        </div>
    </div>
</div>
