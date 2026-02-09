---
title: Sistema de Permissões - Documento Técnico
type: note
permalink: spadaer/docs/sistema-de-permissoes-documento-tecnico
tags:
- permissões
- RBAC
- spatie
- autorização
- segurança
- documentação técnica
- spadaer
---

# Sistema de Permissões SPADAER — Documento Técnico

## 1. Visão Geral

O sistema de permissões do SPADAER implementa um modelo **RBAC (Role-Based Access Control)** utilizando o pacote **Spatie Laravel Permission v6** integrado ao Laravel 12. A arquitetura adota uma estratégia de **defesa em profundidade** com 4 camadas de autorização: Middleware, Gates, Policies e Trait auxiliar.

- [Sistema] utiliza [Spatie Laravel Permission v6]
- [Sistema] implementa [RBAC - Role-Based Access Control]
- [Arquitetura] adota [Defesa em Profundidade com 4 camadas]

---

## 2. Stack Tecnológica

| Componente | Tecnologia | Versão |
|---|---|---|
| Framework | Laravel | 12.50.0 |
| PHP | PHP | 8.4.17 |
| Permissões | Spatie Laravel Permission | 6.x |
| Frontend Reativo | Livewire | 4.1.3 |
| CSS | Tailwind CSS | 4.1.18 |
| Interatividade | Alpine.js | 3.15.8 |
| Banco de Dados | MySQL | — |

- [Stack] inclui [Laravel 12, Livewire 4, Tailwind CSS 4, Alpine.js 3]
- [Banco de Dados] é [MySQL]

---

## 3. Arquitetura do Banco de Dados

### 3.1 Diagrama Entidade-Relacionamento (Textual)

```
┌──────────────┐     ┌───────────────────────┐     ┌──────────────┐
│    users      │     │   model_has_roles      │     │    roles      │
│──────────────│     │───────────────────────│     │──────────────│
│ id (PK)       │◄───│ model_id (FK)          │───►│ id (PK)       │
│ name          │     │ model_type             │     │ name          │
│ rank          │     │ role_id (FK)           │     │ display_name  │
│ full_name     │     └───────────────────────┘     │ guard_name    │
│ order_number  │                                     └──────┬───────┘
│ email         │     ┌───────────────────────┐            │
│ password      │     │ model_has_permissions  │     ┌──────┴───────────┐
└──────────────┘     │───────────────────────│     │role_has_permissions│
                      │ model_id (FK)          │     │──────────────────│
                      │ model_type             │     │ role_id (FK)      │
                      │ permission_id (FK)     │     │ permission_id (FK)│
                      └──────────┬────────────┘     └────────┬─────────┘
                                 │                            │
                                 ▼                            ▼
                         ┌──────────────┐             ┌──────────────┐
                         │  permissions  │◄────────────│  permissions  │
                         │──────────────│             └──────────────┘
                         │ id (PK)       │
                         │ name          │
                         │ guard_name    │
                         └──────────────┘
```

### 3.2 Tabelas

| Tabela | Descrição | Chave Primária |
|---|---|---|
| `permissions` | Todas as permissões do sistema | `id` (bigint) |
| `roles` | Papéis/funções do sistema | `id` (bigint) |
| `model_has_permissions` | Permissões diretas atribuídas a usuários | Composta (`permission_id`, `model_id`, `model_type`) |
| `model_has_roles` | Papéis atribuídos a usuários | Composta (`role_id`, `model_id`, `model_type`) |
| `role_has_permissions` | Permissões associadas a cada papel | Composta (`permission_id`, `role_id`) |

### 3.3 Índices e Foreign Keys

- Todas as tabelas pivô possuem **foreign keys com CASCADE DELETE**
- Índice único em `permissions(name, guard_name)` e `roles(name, guard_name)`
- Índice em `model_has_roles(model_id, model_type)` e `model_has_permissions(model_id, model_type)`

### 3.4 Migrations

- `database/migrations/2025_05_26_171816_create_permission_tables.php` — Cria as 5 tabelas da Spatie
- `database/migrations/2025_05_27_000000_add_display_name_to_roles_table.php` — Adiciona campo `display_name` nullable à tabela `roles`

- [Tabela permissions] armazena [Permissões do sistema]
- [Tabela roles] armazena [Papéis do sistema]
- [Tabela model_has_roles] relaciona [Usuários com Papéis]
- [Tabela model_has_permissions] relaciona [Usuários com Permissões diretas]
- [Tabela role_has_permissions] relaciona [Papéis com Permissões]

---

## 4. Papéis (Roles)

O sistema define **4 papéis** com hierarquia implícita de permissões:

| ID | Slug | Display Name | Descrição |
|---|---|---|---|
| 1 | `admin` | Administrador | Acesso total ao sistema |
| 2 | `user` | Usuário | Acesso básico de visualização e gestão de caixas |
| 3 | `commission_president` | Presidente de Comissão | Gestão de documentos e edição de comissões |
| 4 | `commission_member` | Membro de Comissão | Visualização e criação de documentos |

### Proteções do Sistema

Os papéis `admin` e `user` são **protegidos** — não podem ser excluídos nem renomeados. Enforçado no componente `RoleManager`.

- [Role admin] é [Protegido contra exclusão e renomeação]
- [Role user] é [Protegido contra exclusão e renomeação]

---

## 5. Permissões

### 5.1 Catálogo Completo (20 permissões em 5 módulos)

#### Módulo: Usuários (`users.*`)
| Permissão | Descrição |
|---|---|
| `users.view` | Visualizar listagem de usuários |
| `users.create` | Criar novos usuários |
| `users.edit` | Editar usuários existentes |
| `users.delete` | Excluir usuários |

#### Módulo: Documentos (`documents.*`)
| Permissão | Descrição |
|---|---|
| `documents.view` | Visualizar documentos |
| `documents.create` | Criar novos documentos |
| `documents.edit` | Editar documentos |
| `documents.delete` | Excluir documentos |
| `documents.export.excel` | Exportar para Excel |
| `documents.export.pdf` | Exportar para PDF |
| `documents.import` | Importar documentos |
| `documents.view.secret` | Visualizar documentos sigilosos |

#### Módulo: Comissões (`commissions.*`)
| Permissão | Descrição |
|---|---|
| `commissions.view` | Visualizar comissões |
| `commissions.create` | Criar comissões |
| `commissions.edit` | Editar comissões |
| `commissions.delete` | Excluir comissões |

#### Módulo: Caixas (`boxes.*`)
| Permissão | Descrição |
|---|---|
| `boxes.view` | Visualizar caixas |
| `boxes.create` | Criar caixas |
| `boxes.edit` | Editar caixas |
| `boxes.delete` | Excluir caixas |

### 5.2 Padrão de Nomenclatura

Formato: **`módulo.ação`** (dot notation). Sub-ações: **`módulo.ação.detalhe`** (ex: `documents.export.excel`).

- [Permissões] seguem [Padrão dot notation: módulo.ação]
- [Sistema] possui [20 permissões em 5 módulos]

---

## 6. Matriz de Permissões por Papel

| Permissão | Admin | Usuário | Pres. Comissão | Membro Comissão |
|---|:---:|:---:|:---:|:---:|
| users.view | ✅ | ❌ | ❌ | ❌ |
| users.create | ✅ | ❌ | ❌ | ❌ |
| users.edit | ✅ | ❌ | ❌ | ❌ |
| users.delete | ✅ | ❌ | ❌ | ❌ |
| documents.view | ✅ | ✅ | ✅ | ✅ |
| documents.create | ✅ | ❌ | ✅ | ✅ |
| documents.edit | ✅ | ❌ | ✅ | ❌ |
| documents.delete | ✅ | ❌ | ❌ | ❌ |
| documents.export.excel | ✅ | ❌ | ✅ | ✅ |
| documents.export.pdf | ✅ | ❌ | ✅ | ✅ |
| documents.import | ✅ | ❌ | ❌ | ❌ |
| documents.view.secret | ✅ | ❌ | ❌ | ❌ |
| commissions.view | ✅ | ✅ | ✅ | ✅ |
| commissions.create | ✅ | ❌ | ❌ | ❌ |
| commissions.edit | ✅ | ❌ | ✅ | ❌ |
| commissions.delete | ✅ | ❌ | ❌ | ❌ |
| boxes.view | ✅ | ✅ | ❌ | ❌ |
| boxes.create | ✅ | ✅ | ❌ | ❌ |
| boxes.edit | ✅ | ✅ | ❌ | ❌ |
| boxes.delete | ✅ | ✅ | ❌ | ❌ |

### Resumo Quantitativo

| Papel | Permissões | % do Total |
|---|---|---|
| Admin | 20/20 | 100% |
| Usuário | 6/20 | 30% |
| Presidente de Comissão | 7/20 | 35% |
| Membro de Comissão | 5/20 | 25% |

- [Admin] possui [20/20 permissões - 100%]
- [Usuário] possui [6/20 permissões - 30%]
- [Presidente de Comissão] possui [7/20 permissões - 35%]
- [Membro de Comissão] possui [5/20 permissões - 25%]

---

## 7. Camadas de Autorização (Defesa em Profundidade)

```
┌─────────────────────────────────────────────────┐
│  CAMADA 1: Middleware (Nível de Rota)            │
│  CheckRole / CheckPermission                     │
├─────────────────────────────────────────────────┤
│  CAMADA 2: Gates (Nível Global)                  │
│  AuthServiceProvider → Gate::define()            │
├─────────────────────────────────────────────────┤
│  CAMADA 3: Policies (Nível de Modelo)            │
│  DocumentPolicy (lógica de sigilo)               │
├─────────────────────────────────────────────────┤
│  CAMADA 4: Trait HasAuthorization (Componentes)  │
│  Verificações inline em Livewire Components      │
└─────────────────────────────────────────────────┘
```

### 7.1 Camada 1 — Middleware

**CheckPermission** (`app/Http/Middleware/CheckPermission.php`): Verifica permissão específica via `hasPermissionTo()`. Alias: `permission`.

**CheckRole** (`app/Http/Middleware/CheckRole.php`): Verifica papel(is) via `hasAnyRole()`. Suporta múltiplos papéis. Alias: `role`.

**Uso nas rotas:**
```php
Route::middleware('role:admin')->prefix('admin')->group(...);
Route::post('/')->middleware('role:admin,presidente_comissao');
```

### 7.2 Camada 2 — Gates

**AuthServiceProvider** (`app/Providers/AuthServiceProvider.php`): Registra Gates dinâmicos para todas as 20+ permissões. Cada Gate delega ao `hasPermissionTo()` da Spatie. Inclui logging em modo debug.

### 7.3 Camada 3 — Policies

**DocumentPolicy** (`app/Policies/DocumentPolicy.php`): Autorização granular para Documents com sistema de sigilo.

| Método | Permissão | Lógica Especial |
|---|---|---|
| `viewAny` | `documents.view` | — |
| `view` | `documents.view` + `documents.view.secret` | Verifica sigilo |
| `create` | `documents.create` | — |
| `update` | `documents.edit` | — |
| `delete` | `documents.delete` | — |

### 7.4 Camada 4 — Trait HasAuthorization

**HasAuthorization** (`app/Traits/HasAuthorization.php`): Métodos auxiliares para componentes Livewire: `userHasRole()`, `userHasPermission()`, `userHasAnyRole()`, `userHasAnyPermission()`.

- [Camada 1 Middleware] protege [Rotas HTTP]
- [Camada 2 Gates] protege [Ações globais via authorize()]
- [Camada 3 Policies] protege [Operações em modelos específicos]
- [Camada 4 Trait] protege [Lógica de componentes Livewire]

---

## 8. Models

### User (`app/Models/User.php`)

Usa trait `HasRoles` da Spatie. Métodos disponíveis: `assignRole()`, `removeRole()`, `syncRoles()`, `hasRole()`, `hasAnyRole()`, `givePermissionTo()`, `revokePermissionTo()`, `hasPermissionTo()`.

### Role (`app/Models/Role.php`)

Estende `Spatie\Permission\Models\Role` com campo customizado `display_name`.

- [User] usa trait [HasRoles da Spatie]
- [Role] estende [SpatieRole com display_name]

---

## 9. Seeders

### Ordem de Execução (DatabaseSeeder)

```
1. PermissionSeeder    → 20 permissões
2. RoleSeeder          → 4 papéis com permissões
3. UserSeeder          → Usuários de teste
4. CommissionMemberSeeder → Membros de comissões
5. ProjectSeeder       → Projetos
6. BoxSeeder           → Caixas
7. DocumentSeeder      → Documentos
```

- [PermissionSeeder] cria [20 permissões]
- [RoleSeeder] cria [4 papéis com permissões]

---

## 10. Interface Administrativa

### Rotas (protegidas por `middleware('role:admin')`)

| Rota | Componente | Funcionalidade |
|---|---|---|
| `GET /admin/users` | `UserList` | Gestão de usuários, papéis e permissões granulares |
| `GET /admin/roles` | `RoleManager` | Listagem e criação de papéis |
| `GET /admin/roles/{role}/edit` | `RoleEdit` | Edição de papel e matriz de permissões |
| `GET /admin/audit` | View `audit` | Logs de auditoria |

### Componentes Livewire

**UserList**: Listagem paginada, busca em tempo real, criação/edição via modal, atribuição de papéis e permissões granulares por categoria.

**RoleManager**: Cards responsivos, criação via modal, proteção de papéis do sistema, redirect para edição após criação.

**RoleEdit**: Edição de nome (exceto protegidos), matriz de permissões por categoria, resumo visual de acesso, `syncPermissions()`.

- [Painel Admin] é protegido por [Middleware role:admin]
- [UserList] gerencia [Usuários, papéis e permissões granulares]
- [RoleManager] gerencia [Criação e exclusão de papéis]
- [RoleEdit] gerencia [Permissões por papel via matriz interativa]

---

## 11. Sistema de Sigilo

Fluxo de verificação para documentos sigilosos:

```
Usuário acessa documento
        │
        ▼
   Tem documents.view? ── NÃO ──► BLOQUEADO
        │
       SIM
        │
        ▼
   É documento secreto? ── NÃO ──► LIBERADO
        │
       SIM
        │
        ▼
   Tem documents.view.secret? ── NÃO ──► BLOQUEADO
        │
       SIM ──► LIBERADO
```

Apenas **Admin** possui `documents.view.secret` por padrão.

- [Sistema de Sigilo] usa [Permissão documents.view.secret]
- [DocumentPolicy] verifica [Sigilo via método isSecret()]

---

## 12. Auditoria

Trait `Auditable` (`app/Traits/Auditable.php`) registra automaticamente:

| Evento | Dados |
|---|---|
| `created` | Atributos do novo registro |
| `updated` | Valores antigos e novos (dirty fields) |
| `deleted` | Atributos do registro excluído |
| `restored` | Evento de restauração |

Cada log inclui: `user_id`, `ip_address`, `user_agent`, `old_values`, `new_values`.

- [Trait Auditable] registra [Eventos created, updated, deleted, restored]

---

## 13. Configuração (config/permission.php)

| Configuração | Valor |
|---|---|
| Modelo de Role | `App\Models\Role` (customizado) |
| Modelo de Permission | `Spatie\Permission\Models\Permission` (padrão) |
| Guard padrão | `web` |
| Teams | Desabilitado |
| Wildcard permissions | Desabilitado |
| Cache | 24 horas |
| Eventos | Desabilitados |

---

## 14. Inventário de Arquivos

| Arquivo | Responsabilidade |
|---|---|
| `app/Models/User.php` | Model com trait HasRoles |
| `app/Models/Role.php` | Model com display_name |
| `app/Http/Middleware/CheckPermission.php` | Middleware de permissão |
| `app/Http/Middleware/CheckRole.php` | Middleware de papel |
| `app/Traits/HasAuthorization.php` | Trait auxiliar |
| `app/Traits/Auditable.php` | Trait de auditoria |
| `app/Providers/AuthServiceProvider.php` | Gates e Policies |
| `app/Policies/DocumentPolicy.php` | Policy com sigilo |
| `config/permission.php` | Configuração Spatie |
| `app/Livewire/Admin/UserList.php` | Gestão de usuários |
| `app/Livewire/Admin/RoleManager.php` | Gestão de papéis |
| `app/Livewire/Admin/RoleEdit.php` | Edição de papel |
| `resources/views/livewire/admin/user-list.blade.php` | UI usuários |
| `resources/views/livewire/admin/role-manager.blade.php` | UI papéis |
| `resources/views/livewire/admin/role-edit.blade.php` | UI edição papel |
| `database/seeders/PermissionSeeder.php` | Seed permissões |
| `database/seeders/RoleSeeder.php` | Seed papéis |
| `database/migrations/2025_05_26_171816_create_permission_tables.php` | Migration tabelas |
| `database/migrations/2025_05_27_000000_add_display_name_to_roles_table.php` | Migration display_name |
