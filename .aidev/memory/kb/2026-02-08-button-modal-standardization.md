# Lição: Padronização de Botões de Ação e Integração com Modais Alpine.js

**Data**: 2026-02-08
**Stack**: Blade, Alpine.js, Livewire
**Tags**: excellence-pattern, ui-standardization, ux

## Contexto
O sistema possuía diversos métodos de confirmação de exclusão (confirm() do navegador, forms diretos, modais Livewire dispersos) gerando uma experiência de usuário inconsistente e visualmente datada.

## Problema
Modais de confirmação via `@confirm` do Livewire ou `confirm()` do JS são limitados visualmente e difíceis de estilizar conforme o design premium `x-ui`. Além disso, gerenciavam estado de forma fragmentada.

## Solução
### Correção Aplicada (Padrão de Sucesso)
Implementação de um **Modal de Confirmação Global** gerenciado por um **Alpine Store**.

1.  **Componente Único**: `<x-ui.confirmation-modal />` incluído apenas uma vez no layout principal.
2.  **Estado Centralizado**: Uso de `Alpine.store('modals').confirmDelete` para controlar visibilidade, título, mensagem e ação.
3.  **Flexibilidade de Ação**: O modal suporta:
    - Submissão de formulários externos via `submitFormId`.
    - Chamadas de callback via `onConfirm`.
    - Submissão direta de formulário interno via `action`.

```blade
{{-- Uso Prático --}}
<x-ui.button 
    variant="danger" 
    @click="$store.modals.openDeleteConfirmation({
        submitFormId: 'delete-form-{{ $id }}',
        title: 'Excluir Registro',
        message: 'Esta ação é irreversível.'
    })"
>
```

### Por Que Funciona
- **Performance**: Apenas um componente no DOM para todos os modais de confirmação.
- **Consistência**: Todos os diálogos de alerta seguem o mesmo design e animações.
- **Desacoplamento**: O botão disparador não precisa conhecer a lógica do modal, apenas passar a configuração.

## Prevenção
- [ ] Evitar o uso de `confirm()` nativo.
- [ ] Centralizar estados de UI efêmeros (modais, tooltips, notificações) em stores do Alpine.
- [ ] Manter o componente de modal no nível mais alto do layout para evitar problemas de `overflow`.

## Referências
- [Componente Confirmation Modal](file:///home/nandodev/projects/spadaer/resources/views/components/ui/confirmation-modal.blade.php)
- [Alpine Store Init](file:///home/nandodev/projects/spadaer/resources/js/app.js)
