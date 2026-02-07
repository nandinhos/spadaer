# Licao: Unicidade Composta em Tabelas de Relacionamento (Pivot)

**Data**: 2026-02-07
**Stack**: Laravel 12, MySQL
**Tags**: success-pattern, database, migration, fix

## Contexto
O sistema possui comissões e membros. Um membro (`commission_members`) liga um usuário (`users`) a uma comissão (`commissions`).

## Problema
A migração inicial definiu `$table->unique('user_id')`. Isso causava o erro `SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry` sempre que se tentava adicionar o mesmo usuário a uma segunda comissão diferente, pois o banco permitia apenas um registro por usuário na tabela inteira.

## Causa Raiz
Uso de restrição de unicidade simples em uma coluna que, por regra de negócio, deveria permitir repetição desde que associada a um pai (`commission_id`) diferente.

## Solução
Alterar a restrição para uma chave única composta.

**Migração de Correção:**
```php
Schema::table('commission_members', function (Blueprint $table) {
    // É necessário remover a FK antes de dropar o índice se o índice for usado pela FK
    $table->dropForeign(['user_id']);
    $table->dropUnique(['user_id']);
    
    // Nova restrição: usuário único POR comissão
    $table->unique(['user_id', 'commission_id']);
    
    // Re-adicionar a FK
    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
});
```

## Prevenção
- Em tabelas pivot ou de membros, sempre avalie se a unicidade deve ser global ou relativa ao pai.
- Use `$table->unique(['fk1', 'fk2'])` para garantir integridade sem bloquear o uso do modelo em outros contextos.
