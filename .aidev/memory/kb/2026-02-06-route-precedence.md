# Lição: Precedência de Rotas (Static vs Wildcard) no Laravel

**Data**: 2026-02-06
**Stack**: Laravel 10/11/12
**Tags**: routing, bug, success-pattern

## Contexto
Durante a implementação do módulo de documentos, as rotas `/create`, `/export` e `/import` estavam retornando erro 404 ou tentando tratar a string como um ID de modelo.

## Problema
O Laravel lê o arquivo de rotas de cima para baixo. Se uma rota com wildcard (ex: `/{document}`) for definida antes de uma rota estática (ex: `/create`), o wildcard capturará o caminho estático.

### Exemplo de Erro
```php
Route::get('/{document}', [DocumentController::class, 'show']);
Route::get('/create', [DocumentController::class, 'create']); // NUNCA SERÁ ALCANÇADA
```

## Causa Raiz
O resolvedor de rotas do Laravel encontra a primeira correspondência que satisfaça o padrão. Como `create` é uma string válida para o parâmetro `{document}`, ele entra na primeira rota e falha ao tentar converter a string "create" em um modelo no banco de dados.

## Solução
### Correção Aplicada
Sempre definir rotas estáticas **antes** das rotas com parâmetros dinâmicos dentro do mesmo prefixo/recurso.

```php
Route::prefix('documents')->group(function () {
    // 1. Rotas Estáticas (Prioridade)
    Route::get('/create', [DocumentController::class, 'create']);
    Route::get('/export', [DocumentController::class, 'export']);
    
    // 2. Rotas Dinâmicas / Wildcards
    Route::get('/{document}', [DocumentController::class, 'show']);
});
```

## Prevenção
- [ ] Listar rotas estáticas primeiro em todos os grupos.
- [ ] Usar restrições de regex (`whereNumber`, `whereAlpha`) em wildcards para diminuir ambiguidades.
- [ ] Validar rotas com `php artisan route:list` para verificar a ordem de precedência.

## Referências
- [Laravel Routing - Global Constraints](https://laravel.com/docs/11.x/routing#parameters-regular-expression-constraints)
