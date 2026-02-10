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
**Status:** ✅ Concluído
- Extrair lógica de filtragem e estatísticas para `DocumentService`.

##### 4.4 - Melhoria de Performance em Consultas
**Prioridade:** 🟢 MÉDIA
**Status:** 🟡 Pendente
- Otimizar query do Index e tratar ordenação cronológica de `document_date`.

---

### 📅 SPRINT 5: Padronização de UI — Sistema de Botões (Próximo)
**Objetivo:** Migrar todos os 83 botões (47 Breeze + 24 links + 12 HTML) em 34 arquivos para o componente padrão `<x-ui.button>`, garantindo consistência visual em todo o sistema.
**Status:** 🔵 Em Progresso (Aguardando Verificação Final)

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
**Status:** ✅ Concluído
**Arquivos:** `boxes/index.blade.php`, `projects/show.blade.php`

##### 5.2 - Padronização: Páginas de Detalhe (Show)
**Prioridade:** 🟡 ALTA
**Status:** ✅ Concluído
**Arquivos:** `boxes/show.blade.php`, `commissions/show.blade.php`, `documents/show.blade.php`

##### 5.3 - Padronização: Formulários (Create/Edit)
**Prioridade:** 🟡 ALTA
**Status:** ✅ Concluído
**Arquivos:** 8 formulários — boxes, commissions, documents, projects (create + edit)

##### 5.4 - Padronização: Componentes Reutilizáveis
**Prioridade:** 🟢 MÉDIA
**Status:** ✅ Concluído
**Arquivos:** `document-modal.blade.php`, `document-filters.blade.php`, `document-table.blade.php`

##### 5.5 - Padronização: Auth, Profile e Admin
**Prioridade:** 🟢 MÉDIA
**Status:** ✅ Concluído
**Arquivos:** 6 auth + 3 profile + 1 admin (17 botões total)

---

## 📅 SPRINT 6: Infraestrutura de Auditoria
**Objetivo:** Implementar logs de auditoria e segurança.
**Status:** ✅ Concluído

#### Funcionalidades:
- **6.1 - Estratégia de Auditoria**: Definir pacotes e migrações. ✅
- **6.2 - Log de Ações**: Implementar Trait/Observers. ✅
- **6.3 - Interface Admin Master**: Visualização de logs. ✅

---

## 📅 SPRINT 7: Segurança e Sigilo (Fase 3)
**Objetivo:** Implementar camada de segurança avançada e controle de sigilo de dados.
**Status:** ⏸️ Pausado

> **Motivo da pausa:** Sprint adiado para outro momento devido à complexidade envolvida e à necessidade de alterações no core do sistema. Será retomado quando houver janela adequada para mudanças estruturais.

---

## 📊 RESUMO DE PRIORIDADES

| Sprint | Funcionalidade | Prioridade | Status |
|--------|----------------|------------|--------|
| 5 | Conclusão Padronização UI | 🔴 CRÍTICA | ✅ Concluído |
| 4 | Melhoria Performance Consultas | 🟢 MÉDIA | 🟡 Pendente |
| 6 | Infraestrutura de Auditoria | 🔴 CRÍTICA | ✅ Concluído |
| 7 | Segurança e Sigilo (Fase 3) | 🟡 ALTA | ⏸️ Pausado |

---

**Versão:** 1.3 (v3.7)
**Status:** Ativo
**Última atualização:** 2026-02-09