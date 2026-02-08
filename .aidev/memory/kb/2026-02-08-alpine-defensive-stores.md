# Lição: Inicialização Defensiva de Alpine.js Stores e Correção de Modais

**Data**: 2026-02-08
**Stack**: Laravel 12, Alpine.js, Blade
**Tags**: alpinejs, bug, success-pattern, frontend

## Contexto
Durante o refinamento da UI Premium, os modais de confirmação globais (que dependem de um Alpine Store) apresentavam erros de "undefined" ao serem carregados, pois o Alpine tentava renderizar expressões do store antes da inicialização completa do JavaScript.

## Problema
### Sintoma Observado
Erro no console: `Alpine Expression Error: Cannot read properties of undefined (reading 'show')`.
Além disso, ocorria o problema da "tela cinza" (backdrop ativo mas modal oculto ou atrás do backdrop).

### Evidência
```
Expression: "$store.modals.confirmDelete.show"
```

## Causa Raiz
### Análise (5 Whys)
1. **Por que falhou?** O Alpine tentou acessar `$store.modals` e não encontrou o objeto.
2. **Por que?** O componente Blade foi renderizado e processado pelo Alpine antes que o script `app.js` registrasse o store.
3. **Por que?** A ordem de carregamento de scripts asíncronos e a reidratação do Livewire podem criar condições de corrida.
4. **Por que não houve proteção?** As expressões Blade/Alpine assumiam que o store sempre estaria presente.

## Solução
### Correção Aplicada
Implementação de **Optional Chaining** e **Inicialização Defensiva** no componente de modal.

```blade
{{-- resources/views/components/ui/confirmation-modal.blade.php --}}
<div
    x-data="{ init() { if (!Alpine.store('modals')) { Alpine.store('modals', { confirmDelete: { show: false } }); } } }"
    x-show="$store.modals?.confirmDelete?.show || false"
    x-trap.noscroll="$store.modals?.confirmDelete?.show || false"
    ...
>
```

### Por Que Funciona
- O `Optional Chaining` (`?.`) evita o crash se o objeto for nulo.
- O `|| false` ou `|| 'Padrao'` garante um valor seguro para as diretivas do Alpine.
- O `init()` no `x-data` cria um "fallback store" local caso o global ainda não tenha carregado.

## Prevenção
- [ ] Sempre usar `?.` ao acessar `$store` em componentes globais.
- [ ] Usar `x-cloak` para evitar flicker de elementos ocultos.
- [ ] Garantir `z-index` hierárquico (ex: modals em `z-50`, overlays em `z-[60]`).

## Referências
- [Documentação Alpine Store](https://alpinejs.dev/globals/store)
- [Optional Chaining JS](https://developer.mozilla.org/pt-BR/docs/Web/JavaScript/Reference/Operators/Optional_chaining)
