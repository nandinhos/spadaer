# Lição: Implementação de Auditoria Polimórfica com Eloquent Traits

**Data**: 2026-02-08
**Stack**: Laravel 12, Eloquent, PHP 8.4
**Tags**: success-pattern, architecture, audit, security

## Contexto
Necessidade de registrar todas as ações de criação, atualização e exclusão (CUD) nas entidades principais do sistema (Documentos, Caixas, Projetos e Comissões) para fins de conformidade e segurança.

## Problema
Implementar auditoria manual em cada Controller é propenso a erros, causa duplicação de código e dificulta a manutenção ao adicionar novos modelos.

## Causa Raiz
Falta de um mecanismo centralizado e automatizado para observar mudanças de estado no ciclo de vida dos modelos Eloquent.

## Solução
### Correção Aplicada (Padrão de Sucesso)
Criação de um sistema baseado em **Eloquent Traits** e **Relacionamentos Polimórficos**.

1.  **Modelo Polimórfico**: `AuditLog` com `auditable_type` e `auditable_id`.
2.  **Trait Reutilizável**: `Auditable` que utiliza o método `bootAuditable` para registrar listeners de eventos (`created`, `updated`, `deleted`).
3.  **Captura de Diferenças**: Uso de `$model->getDirty()` e `$model->getOriginal()` para registrar exatamente o que mudou.

```php
// App\Traits\Auditable.php
static::updated(function ($model) {
    $model->audit('updated');
});
```

### Por Que Funciona
- Centraliza a lógica em um único local.
- A ativação em novos modelos é feita com apenas uma linha: `use Auditable;`.
- Captura metadados do request (IP, User Agent) automaticamente.

## Prevenção
- [ ] Filtrar campos sensíveis (passwords, tokens) do log de auditoria.
- [ ] Limitar o tamanho dos campos `old_values` e `new_values` para evitar estouro de disco em logs massivos.
- [ ] Usar `json` no banco de dados para facilitar consultas futuras.

## Referências
- [Laravel Eloquent Observers](https://laravel.com/docs/11.x/eloquent#observers)
- [Eloquent Model Traits](https://laravel.com/docs/11.x/eloquent#booting-traits)
