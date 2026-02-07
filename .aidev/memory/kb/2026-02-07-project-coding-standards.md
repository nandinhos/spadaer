# Licao: Padroes de Codificacao e Organizacao do Projeto SPADAER

**Data**: 2026-02-07
**Stack**: Laravel 12, Livewire, Blade
**Tags**: success-pattern, architecture, convention

## Contexto
Definição de padrões para garantir manutenibilidade e consistência visual/funcional.

## Padroes Estabelecidos

### 1. Validacao (Form Requests)
- **Regra**: Nunca usar `$request->validate()` diretamente nos controllers.
- **Acao**: Criar classes específicas via `php artisan make:request`.
- **Vantagem**: Limpa o controller e permite reutilizar regras em Update/Store.

### 2. Ordenacao de Dados (Antiguidade)
- **Regra**: Listagens de usuários e membros devem seguir a ordem de ID (ou coluna de rank) ascendente.
- **Implementacao**:
  ```php
  $users = User::orderBy('id', 'asc')->get();
  ```

### 3. Formatacao de Codigo
- **Regra**: Rodar o Laravel Pint antes de finalizar tarefas.
- **Comando**: `vendor/bin/sail bin pint --dirty` (formata apenas arquivos alterados).

### 4. UI/UX (Sidebar e Componentes)
- **Regra**: Manter consistência visual usando componentes Blade.
- **Localizacao**: `resources/views/components/`.

## Prevencao
- Revisar se novos controllers seguem o padrão de Form Requests.
- Verificar se novas tabelas possuem ordenação padrão amigável ao usuário.
