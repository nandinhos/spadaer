# 🗺️ ROADMAP DE IMPLEMENTAÇÃO - laravel

> Documento mestre de planejamento de funcionalidades
> Status: Ativo

---

## 📋 VISÃO GERAL

Este projeto usa AI Dev Superpowers para governança e qualidade.
- ✅ TDD Obrigatório
- ✅ Arquitetura Limpa (Services/Actions)
- ✅ Documentação Técnica Viva

---

## 🎯 SPRINTS PLANEJADOS

### 📅 SPRINT 4: Refatoração e Fortalecimento
**Objetivo:** Eliminar conflitos de rotas, extrair lógica de negócio para Services e configurar ferramentas de produtividade (MCP).
**Status:** 🔵 Em Progresso

#### Funcionalidades:

##### 4.1 - Instalação do MCP Laravel Boost
**Prioridade:** 🔴 CRÍTICA
**Status:** ✅ Concluído
- Instalação via Composer e configuração do `.mcp.json` para Sail.

##### 4.2 - Ajuste de Roteamento e Conflitos
**Prioridade:** 🔴 CRÍTICA
**Status:** ✅ Concluído
- Reorganizar `routes/web.php` conforme Guia de Insights.

##### 4.3 - Refatoração do DocumentController (Camada de Service)
**Prioridade:** 🟡 ALTA
**Status:** 🟡 Pendente
- Extrair lógica de filtragem e estatísticas para `DocumentService`.

##### 4.4 - Melhoria de Performance em Consultas
**Prioridade:** 🟢 MÉDIA
**Status:** 🟡 Pendente
- Otimizar query do Index e tratar ordenação cronológica de `document_date`.

---

### 📅 SPRINT 5: Padronização de UI — Sistema de Botões (Próximo)
**Objetivo:** Migrar todos os 83 botões (47 Breeze + 24 links + 12 HTML) em 34 arquivos para o componente padrão `<x-ui.button>`, garantindo consistência visual em todo o sistema.
**Status:** 🟡 Planejado

#### Referência de Variantes:
| Contexto | Variante | Tamanho | Ícone |
|----------|---------|---------|-------|
| Ação principal (Salvar/Criar) | `primary` | `md` | — |
| Ação secundária (Cancelar/Voltar) | `secondary` | `md` | `fa-arrow-left` |
| Ação destrutiva (Excluir) | `danger` | `md` | `fa-trash-alt` |
| Exportar | `success` | `sm` | `fa-file-export` |
| Importar | `warning` | `sm` | `fa-file-import` |
| Ver (tabela) | `ghost-primary` | `sm` | `fa-eye` |
| Editar (tabela) | `ghost-warning` | `sm` | `fa-edit` |
| Excluir (tabela) | `ghost-danger` | `sm` | `fa-trash` |

#### Funcionalidades:

##### 5.1 - Padronização: Listagens Principais
**Prioridade:** 🔴 CRÍTICA
**Status:** 🟡 Pendente
**Arquivos:** `boxes/index.blade.php` (8 botões), `projects/show.blade.php` (2 botões)
- Plano detalhado: `.aidev/plans/features/5.1-button-standardization-listings.md`

##### 5.2 - Padronização: Páginas de Detalhe (Show)
**Prioridade:** 🟡 ALTA
**Status:** 🟡 Pendente
**Arquivos:** `boxes/show.blade.php` (7), `commissions/show.blade.php` (3), `documents/show.blade.php` (5)
- Plano detalhado: `.aidev/plans/features/5.2-button-standardization-show-pages.md`

##### 5.3 - Padronização: Formulários (Create/Edit)
**Prioridade:** 🟡 ALTA
**Status:** 🟡 Pendente
**Arquivos:** 8 formulários — boxes, commissions, documents, projects (create + edit)
- Plano detalhado: `.aidev/plans/features/5.3-button-standardization-forms.md`

##### 5.4 - Padronização: Componentes Reutilizáveis
**Prioridade:** 🟢 MÉDIA
**Status:** 🟡 Pendente
**Arquivos:** `document-modal.blade.php` (2), `document-filters.blade.php` (2), `document-table.blade.php` (4)
- Plano detalhado: `.aidev/plans/features/5.4-button-standardization-components.md`

##### 5.5 - Padronização: Auth, Profile e Admin
**Prioridade:** 🟢 MÉDIA
**Status:** 🟡 Pendente
**Arquivos:** 6 auth + 3 profile + 1 admin (17 botões total)
- Plano detalhado: `.aidev/plans/features/5.5-button-standardization-auth-profile-admin.md`

---

## 📊 RESUMO DE PRIORIDADES

| Sprint | Funcionalidade | Prioridade | Status |
|--------|----------------|------------|--------|
| 4 | Setup MCP Laravel Boost | 🔴 CRÍTICA | ✅ Concluído |
| 4 | Fix Conflito de Rotas | 🔴 CRÍTICA | ✅ Concluído |
| 4 | Refactor DocumentController | 🟡 ALTA | 🟡 Pendente |
| 4 | Melhoria Performance Consultas | 🟢 MÉDIA | 🟡 Pendente |
| 5 | Padronização: Listagens | 🔴 CRÍTICA | 🟡 Pendente |
| 5 | Padronização: Páginas Show | 🟡 ALTA | 🟡 Pendente |
| 5 | Padronização: Formulários | 🟡 ALTA | 🟡 Pendente |
| 5 | Padronização: Componentes | 🟢 MÉDIA | 🟡 Pendente |
| 5 | Padronização: Auth/Profile/Admin | 🟢 MÉDIA | 🟡 Pendente |

---

**Versão:** 1.2 (v3.7)
**Status:** Ativo
**Última atualização:** 2026-02-07
