<?php

return [

    /*
    |---------------------------------------------------------------------------
    | Auto-inject Frontend Assets
    |---------------------------------------------------------------------------
    |
    | Desativado porque estamos fazendo bundle manual do Livewire + Alpine
    | via resources/js/app.js (importando livewire.esm).
    | O layout usa @livewireScriptConfig em vez de injeção automática.
    |
    */

    'inject_assets' => false,

];
