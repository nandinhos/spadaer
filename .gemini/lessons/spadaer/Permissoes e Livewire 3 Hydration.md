---
title: Permissoes e Livewire 3 Hydration
type: lesson_learned
permalink: spadaer/permissoes-e-livewire-3-hydration
---

# Lições Aprendidas: Refatoração de Permissões (Laravel + Livewire 3)

### Core Technical Insight: Livewire 3 Serialization
Ao trabalhar com Livewire 3, evite expor `public $collection` de modelos Eloquent se o componente possuir lógica de renderização complexa. Dispara `BadMethodCallException` relacionado a `getMorphClass`.
**Melhor Prática:** Hidratar coleções no `render()`.

### Padrão de Design: Matriz de Permissões
Agrupar permissões por prefixo (ex: `documents.*`, `commissions.*`) e exibir em grids categorizados melhora drasticamente a usabilidade administrativa em comparação a listas simples ou modais apertados.

### Proteção de Sistema
Sempre implementar guardas explícitas para papéis protegidos:
```php
if (in_array($role->name, ['admin', 'user'])) { // logic }
```
