---
title: RFC - Stack Intelligence Agent para AI Dev Superpowers
type: note
permalink: spadaer/rfcs/rfc-stack-intelligence-agent-para-ai-dev-superpowers
tags:
- rfc
- stack-intelligence
- aidev
- brainstorming
- architecture
- version-management
---

# RFC: Stack Intelligence Agent para AI Dev Superpowers

## Metadata
- **Tipo**: Documento Técnico / Request for Comments
- **Data**: 2026-02-09
- **Projeto Origem**: Spadaer (Laravel 12 TALL Stack)
- **Destinatário**: Skill de Brainstorming do AI Dev Superpowers
- **Prioridade**: Alta
- **Status**: Proposta para Análise

---

## 1. Resumo Executivo

Durante uma auditoria de consistência no projeto Spadaer, foram identificados **8 arquivos com referências incorretas de versão** da stack tecnológica (Livewire 3 em vez de 4, Laravel 10 em vez de 12, PHP 8.2 em vez de 8.4). A investigação revelou que o problema não é pontual — é **sistêmico e estrutural**.

O sistema AI Dev Superpowers opera com 9 agentes e 8 skills, mas **nenhum deles é responsável por rastrear, validar ou sincronizar as versões da stack tecnológica** do projeto. Além disso, o projeto utiliza **6 sistemas de agentes AI em paralelo**, cada um com sua própria forma (ou ausência) de armazenar informações de stack.

Este documento propõe a criação de um **Stack Intelligence Agent** — um agente especializado dedicado à detecção, rastreamento, validação e sincronização de informações de stack tecnológica em todo o ecossistema de desenvolvimento assistido por IA.

---

## 2. Diagnóstico do Problema

### 2.1 Sintomas Observados

| Arquivo | Problema | Versão Incorreta | Versão Correta |
|---------|----------|-------------------|----------------|
| `.aidev/skills/learned-lesson/SKILL.md:59` | Template hardcoded com exemplo desatualizado | `Laravel 10 + PHP 8.2` | `Laravel 12 + PHP 8.4` |
| `.aidev/rules/laravel.md` | Arquivo de regras sem NENHUMA declaração de versão | Ausente | Deveria existir |
| `.serena/memories/project_overview.md:7` | Overview com versão PHP incorreta | `PHP 8.2+` | `PHP 8.4` |
| `conductor/tech-stack.md:4` | Ênfase na constraint em vez da versão real | `PHP ^8.2` (destaque) | `PHP 8.4` (destaque) |
| `resources/js/app.js:83` | Comentário no código referenciando versão antiga | `Livewire 3/4` | `Livewire 4` |
| `.serena/memories/lessons_learned_spadaer_refactor.md:3` | Lição histórica sem anotação de migração | `Livewire 3` | Necessita anotação `3→4` |
| `.gemini/lessons/spadaer/Permissoes e Livewire 3 Hydration.md:7` | Lição histórica sem anotação de migração | `Livewire 3` | Necessita anotação `3→4` |
| `.aidev/memory/kb/2026-02-06-route-precedence.md:4` | Stack genérica com versões antigas | `Laravel 10/11/12` | Contexto válido, mas ambíguo |

### 2.2 Causa Raiz — Análise dos 5 Porquês

```
POR QUE existem versões incorretas nos arquivos?
  → Porque foram criados quando a stack era outra, ou copiados de templates desatualizados.

POR QUE os templates estão desatualizados?
  → Porque o template de learned-lesson (SKILL.md) tem exemplos hardcoded com "Laravel 10 + PHP 8.2".

POR QUE os exemplos estão hardcoded?
  → Porque NÃO EXISTE um mecanismo de injeção dinâmica de versões nos templates.

POR QUE não existe injeção dinâmica?
  → Porque NÃO EXISTE um centro de verdade único para versões da stack no aidev.

POR QUE não existe centro de verdade?
  → Porque NENHUM agente ou skill é responsável por rastrear a stack tecnológica. ← CAUSA RAIZ
```

### 2.3 Classificação do Problema

| Dimensão | Avaliação |
|----------|-----------|
| **Escopo** | Sistêmico — afeta todos os agentes, skills e sistemas paralelos |
| **Frequência** | A cada sessão que gera documentação ou lições aprendidas |
| **Impacto** | Médio-Alto — agentes recebem contexto incorreto, gerando código/docs com referências erradas |
| **Tendência** | Piorará com o tempo — cada upgrade de dependência amplia o gap |
| **Complexidade** | Média — solução clara, mas requer integração com múltiplos sistemas |

---

## 3. Panorama Atual — 6 Sistemas de Agentes AI

O projeto Spadaer possui 6 sistemas de configuração de agentes AI operando em paralelo. Cada um armazena (ou ignora) informações de stack de forma independente.

### 3.1 Mapa de Fragmentação

```
┌─────────────────────────────────────────────────────────┐
│                    PROJETO SPADAER                        │
│                                                          │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐              │
│  │  .aidev   │  │  .serena  │  │  .gemini  │              │
│  │           │  │           │  │           │              │
│  │ session:  │  │ overview: │  │ settings: │              │
│  │ "laravel" │  │ "PHP 8.2+"│  │ (vazio)   │              │
│  │ (sem ver) │  │           │  │           │              │
│  └──────────┘  └──────────┘  └──────────┘              │
│                                                          │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐              │
│  │ conductor │  │  .claude  │  │ .opencode │              │
│  │           │  │           │  │           │              │
│  │ tech-stack│  │ settings: │  │ plans:    │              │
│  │ PHP ^8.2  │  │ (perms)   │  │ (sem ver) │              │
│  │ (melhor)  │  │           │  │           │              │
│  └──────────┘  └──────────┘  └──────────┘              │
│                                                          │
│  ┌──────────────────────────────────────────────────┐   │
│  │         FONTE DE VERDADE REAL (não consultada)    │   │
│  │  composer.json + composer.lock + package.json     │   │
│  └──────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────┘
```

### 3.2 Análise Comparativa por Sistema

| Sistema | Uso Principal | Rastreia Versões? | Como? | Lições Aprendidas | Auto-Detecção |
|---------|---------------|-------------------|-------|-------------------|---------------|
| **.aidev** | Orquestração de agentes | Parcial | `session.json` → `"stack": "laravel"` (sem versão) | `.memory/kb/` com metadata Stack | Nenhuma |
| **.serena** | IDE / análise semântica | Parcial | `project.yml` → `languages: [php]` | `.memories/` markdown livre | Nenhuma |
| **.gemini** | Integração Gemini AI | Nenhum | Delega ao Laravel Boost MCP | `.lessons/` com frontmatter | Nenhuma |
| **conductor** | Workflow de produto | Melhor | `tech-stack.md` detalhado | Embutido em styleguides | Nenhuma |
| **.claude** | Permissões Claude Code | Nenhum | `settings.local.json` (só perms) | Nenhum | Nenhuma |
| **.opencode** | Integração OpenCode | Nenhum | `package.json` (só SDK) | `.plans/` markdown | Nenhuma |

### 3.3 Problemas Identificados

1. **Nenhum sistema consulta `composer.json` / `package.json`** — A fonte de verdade real das versões é completamente ignorada por todos os 6 sistemas.

2. **Zero mecanismo de sincronização** — Se `conductor/tech-stack.md` é atualizado, nenhum outro sistema sabe. Se uma dependência é atualizada via `composer update`, nenhum documento é notificado.

3. **Templates com exemplos hardcoded** — O sistema de skills do aidev usa exemplos fixos (`Laravel 10 + PHP 8.2`) que não evoluem com o projeto.

4. **Lições aprendidas sem versionamento** — Lições escritas durante Livewire 3 não têm mecanismo de anotação quando o projeto migra para Livewire 4. O conhecimento "envelhece" silenciosamente.

5. **Formato inconsistente de metadados** — Cada sistema armazena stack de forma diferente: string simples, YAML, markdown, JSON, ou simplesmente não armazena.

---

## 4. Proposta: Stack Intelligence Agent

### 4.1 Visão

Um **agente especializado** dentro do ecossistema AI Dev Superpowers, responsável exclusivamente por:

- **Detectar** a stack tecnológica real do projeto a partir das fontes de verdade (lock files, configs, runtime)
- **Manter** um registro centralizado e versionado de todas as tecnologias e suas versões
- **Sincronizar** informações de stack com todos os sistemas de agentes (aidev, serena, gemini, conductor, etc.)
- **Validar** documentação existente contra a stack real, identificando drift
- **Historiar** mudanças de stack ao longo do tempo (upgrades, migrações, trocas)
- **Injetar** versões corretas em templates de skills e artefatos gerados

### 4.2 Responsabilidades do Agente

```
┌─────────────────────────────────────────────────────┐
│              STACK INTELLIGENCE AGENT                 │
│                                                      │
│  ┌────────────┐  ┌────────────┐  ┌──────────────┐  │
│  │  DETECTAR   │  │  COMPARAR   │  │  SINCRONIZAR  │  │
│  │             │  │             │  │              │  │
│  │ composer.*  │  │ Real vs     │  │ .aidev       │  │
│  │ package.*   │  │ Documentado │  │ .serena      │  │
│  │ runtime     │  │             │  │ .gemini      │  │
│  │ .env        │  │ Gerar diff  │  │ conductor    │  │
│  └────────────┘  └────────────┘  └──────────────┘  │
│                                                      │
│  ┌────────────┐  ┌────────────┐  ┌──────────────┐  │
│  │  HISTORIAR   │  │  INJETAR    │  │  VALIDAR     │  │
│  │             │  │             │  │              │  │
│  │ Changelog   │  │ Templates   │  │ KB/Lessons   │  │
│  │ de stack    │  │ dinâmicos   │  │ Comments     │  │
│  │ Timeline    │  │ Placeholders│  │ Docs         │  │
│  └────────────┘  └────────────┘  └──────────────┘  │
└─────────────────────────────────────────────────────┘
```

### 4.3 Integração com o Orquestrador

O Stack Intelligence Agent se integra ao orquestrador existente como um **agente de suporte transversal** — ele não é chamado diretamente pelo usuário, mas é consultado por outros agentes e skills.

```
Orquestrador (início de sessão)
    │
    ├─ 1. Ler ROADMAP.md ✓ (existente)
    ├─ 2. Ler unified.json ✓ (existente)
    ├─ 3. Ler session.json ✓ (existente)
    ├─ 4. Ler skills.json ✓ (existente)
    │
    ├─ 5. [NOVO] Consultar Stack Intelligence Agent
    │      ├─ Detectar versões reais (composer.lock, package-lock.json)
    │      ├─ Comparar com stack.json registrada
    │      ├─ Se houver drift → alertar orquestrador
    │      └─ Retornar contexto de stack atualizado
    │
    └─ 6. Saudar usuário com contexto + stack verificada
```

### 4.4 Arquivo de Definição do Agente

**Local**: `.aidev/agents/stack-intelligence.md`

```markdown
# Stack Intelligence Agent

## Role
Agente especializado em detecção, rastreamento e sincronização de
informações de stack tecnológica do projeto. Garante que todos os
agentes, skills e sistemas paralelos operem com informações corretas
e atualizadas sobre as tecnologias em uso.

## Responsabilidades
1. Detecção automática de stack via fontes de verdade
2. Manutenção do registro centralizado (stack.json)
3. Validação de consistência cross-sistema
4. Anotação de lições históricas após migrações
5. Injeção de versões em templates e artefatos

## Fontes de Verdade (ordem de prioridade)
1. composer.lock (versões exatas PHP/Laravel/Livewire)
2. package-lock.json / package.json (versões JS/CSS)
3. Runtime info (php -v, node -v via MCP)
4. .env (database engine, queue driver, etc.)

## Artefato Central
.aidev/state/stack.json — Fonte única de verdade para versões

## Triggers de Ativação
- Início de sessão (consultado pelo orquestrador)
- Detecção de composer update / npm update
- Comando do usuário: "aidev stack-check"
- Pós-upgrade de dependência
- Antes de gerar qualquer artefato com metadata de stack

## Handoff
- Recebe de: Orquestrador (início de sessão), DevOps (pós-deploy)
- Entrega para: Todos os agentes (contexto de stack atualizado)
```

### 4.5 Centro de Verdade Único — stack.json

**Local**: `.aidev/state/stack.json`

Estrutura proposta:

```json
{
  "schema_version": "1.0",
  "last_detected": "2026-02-09T14:30:00Z",
  "detection_source": "composer.lock + package-lock.json",
  "project": {
    "name": "spadaer",
    "type": "laravel-tall"
  },
  "stack": {
    "backend": {
      "language": { "name": "PHP", "version": "8.4.17", "constraint": "^8.2" },
      "framework": { "name": "Laravel", "version": "12.50.0", "constraint": "^12.0" },
      "packages": {
        "livewire/livewire": { "version": "4.1.3", "constraint": "^4.1", "category": "frontend-bridge" },
        "laravel/breeze": { "version": "2.3.6", "constraint": "^2.0", "category": "auth" },
        "spatie/laravel-permission": { "version": "6.x", "constraint": "^6.0", "category": "authorization" }
      }
    },
    "frontend": {
      "css": { "name": "Tailwind CSS", "version": "4.1.18", "constraint": "^4.0.0" },
      "js": { "name": "Alpine.js", "version": "3.15.8", "constraint": "^3.4.2" },
      "bundler": { "name": "Vite", "version": "6.x", "constraint": "^6.0" }
    },
    "infrastructure": {
      "database": { "name": "MySQL", "version": "8.0" },
      "container": { "name": "Laravel Sail", "version": "1.41.0" },
      "os": "Docker (Ubuntu-based)"
    }
  },
  "history": [
    {
      "date": "2026-02-09",
      "event": "initial_detection",
      "changes": []
    }
  ],
  "sync_targets": [
    ".aidev/rules/laravel.md",
    ".aidev/skills/learned-lesson/SKILL.md",
    ".serena/memories/project_overview.md",
    "conductor/tech-stack.md"
  ]
}
```

### 4.6 Skill Associada — stack-audit

**Local**: `.aidev/skills/stack-audit/SKILL.md`

```yaml
---
name: stack-audit
description: Detecta, valida e sincroniza informações de stack tecnológica
triggers: ["stack", "versão", "version", "upgrade", "dependência", "dependency"]
steps: 5
artifact: .aidev/state/stack.json
---
```

**Fluxo da Skill**:

```
Step 1: DETECT (Detecção)
  - Ler composer.lock → extrair versões PHP, Laravel, Livewire, etc.
  - Ler package-lock.json → extrair versões Tailwind, Alpine, Vite
  - Consultar runtime se disponível (php -v, node -v)
  - Gerar stack.json atualizado

Step 2: COMPARE (Comparação)
  - Ler stack.json anterior (se existir)
  - Comparar com detecção atual
  - Gerar diff de mudanças
  - Classificar: upgrade, downgrade, adição, remoção

Step 3: VALIDATE (Validação cross-sistema)
  - Grep por referências de versão em todos os sync_targets
  - Comparar com stack.json detectado
  - Gerar relatório de inconsistências:
    - Arquivo, linha, versão encontrada, versão correta

Step 4: SYNC (Sincronização)
  - Para cada inconsistência encontrada:
    - Propor correção (old_text → new_text)
    - Classificar: template (causa raiz) vs doc (sintoma) vs histórico (anotar)
  - Apresentar plano ao usuário (Protocolo Gatekeeper)
  - Aguardar aprovação antes de executar

Step 5: DOCUMENT (Documentação)
  - Atualizar stack.json com timestamp e changelog
  - Se houve mudanças significativas (upgrade major):
    - Gerar entrada no history[]
    - Sugerir anotação em lições aprendidas afetadas
    - Registrar no ROADMAP.md se aplicável
```

### 4.7 Triggers de Ativação Automática

Adicionar ao `.aidev/triggers/`:

```yaml
# stack-intelligence-triggers.yaml
version: "1.0"

triggers:
  - id: session-start-stack-check
    type: session_start
    action: run_stack_detection
    description: "Detectar stack no início de cada sessão"
    cooldown: 3600  # 1x por hora máximo

  - id: dependency-change-detected
    type: file_change
    watch:
      - "composer.lock"
      - "package-lock.json"
      - "composer.json"
      - "package.json"
    action: run_full_stack_audit
    description: "Auditoria completa quando dependências mudam"
    cooldown: 300

  - id: user-stack-command
    type: user_intent
    keywords: ["stack", "versão", "version", "upgrade", "migração"]
    confidence: 0.8
    action: activate_stack_audit_skill
    cooldown: 60

  - id: artifact-generation-inject
    type: skill_event
    event: "artifact_creating"
    action: inject_stack_versions
    description: "Injetar versões corretas em artefatos sendo gerados"
    cooldown: 0  # Sempre executar
```

---

## 5. Mecanismo de Injeção em Templates

### 5.1 Problema Atual

O template `learned-lesson/SKILL.md` linha 59 contém:
```markdown
**Stack**: [Ex: Laravel 10 + PHP 8.2]
```

Este exemplo é hardcoded e **nunca é atualizado automaticamente**.

### 5.2 Solução Proposta — Placeholders Dinâmicos

Introduzir sintaxe de placeholder nos templates:

```markdown
**Stack**: [Ex: {{stack.backend.framework.name}} {{stack.backend.framework.version_major}} + {{stack.backend.language.name}} {{stack.backend.language.version_major}}]
```

O Stack Intelligence Agent resolveria esses placeholders antes da apresentação ao agente executor.

### 5.3 Resolução em Runtime

```
Template Original:
  **Stack**: [Ex: {{stack.framework}} {{stack.framework_version}} + {{stack.language}} {{stack.language_version}}]

Stack Intelligence resolve via stack.json:
  **Stack**: [Ex: Laravel 12 + PHP 8.4]

Agente executor recebe:
  **Stack**: [Ex: Laravel 12 + PHP 8.4]  ← Sempre correto
```

### 5.4 Fallback para Compatibilidade

Se `stack.json` não existir (projeto sem Stack Intelligence):
- Manter o exemplo hardcoded como fallback
- O agente orquestrador deve alertar: "Stack Intelligence não configurado. Exemplos podem estar desatualizados."

---

## 6. Gestão de Histórico e Migração

### 6.1 Timeline de Stack

O `stack.json` mantém um array `history[]` que registra cada mudança:

```json
{
  "history": [
    { "date": "2026-01-15", "event": "project_created", "changes": ["Laravel 12.0", "Livewire 3.x", "PHP 8.2"] },
    { "date": "2026-01-28", "event": "upgrade", "changes": ["Livewire 3.x → 4.1", "PHP 8.2 → 8.4"] },
    { "date": "2026-02-05", "event": "upgrade", "changes": ["Tailwind 3.x → 4.0"] }
  ]
}
```

### 6.2 Anotação Automática de Lições Históricas

Quando o Stack Intelligence detecta um upgrade (ex: Livewire 3→4), ele deve:

1. **Buscar** lições aprendidas que mencionam a versão antiga
2. **Propor** anotação (não reescrita) com nota de migração:
   ```markdown
   > **Nota (2026-02)**: Projeto migrado para Livewire 4. O padrão descrito continua válido.
   ```
3. **Classificar** se o padrão descrito ainda é válido na nova versão
4. **Registrar** a anotação como ação no histórico

### 6.3 Integração com Lições Aprendidas

A skill `learned-lesson` deveria consultar o Stack Intelligence antes de gerar o template:

```
learned-lesson (Step 1: context_captured)
    │
    ├─ [NOVO] Consultar stack-intelligence
    │   └─ Obter stack atual para preencher metadata
    │
    └─ Gerar template com:
        **Stack**: Laravel 12 + PHP 8.4 + Livewire 4
        (obtido de stack.json, não de exemplo hardcoded)
```

---

## 7. Sincronização Cross-Sistema

### 7.1 Protocolo de Sincronização

O Stack Intelligence Agent mantém uma lista de `sync_targets` — arquivos em todos os sistemas que referenciam versões de stack. Para cada target:

```
1. SCAN: Identificar linhas com referências de versão
2. COMPARE: Contrastar com stack.json
3. CLASSIFY:
   - MATCH → OK, nenhuma ação
   - MISMATCH_TEMPLATE → Causa raiz, corrigir template
   - MISMATCH_DOC → Documentação desatualizada, corrigir
   - MISMATCH_HISTORICAL → Lição antiga, anotar
   - MISMATCH_CONTEXTUAL → Referência válida no contexto (ex: "Laravel 10 structure"), ignorar
4. PROPOSE: Gerar plano de correção
5. EXECUTE: Após aprovação do usuário (Protocolo Gatekeeper)
```

### 7.2 Targets de Sincronização Conhecidos

| Sistema | Arquivo | Tipo de Sincronização |
|---------|---------|----------------------|
| .aidev | `rules/laravel.md` | Seção de versões no topo |
| .aidev | `skills/*/SKILL.md` | Placeholders em exemplos |
| .aidev | `state/session.json` | Campo `stack_versions` |
| .aidev | `memory/kb/*.md` | Metadata `Stack:` em lições |
| .serena | `memories/project_overview.md` | Seção Tech Stack |
| .gemini | `lessons/spadaer/*.md` | Frontmatter e conteúdo |
| conductor | `tech-stack.md` | Documento completo |
| raiz | `GEMINI.md` | Auto-gerado (não tocar) |
| raiz | `README.md` | Badges e seção de stack |

### 7.3 Prioridade de Sincronização

```
1. ALTA: Templates do aidev (causa raiz — afeta futuras gerações)
2. MÉDIA: Documentação de projeto (afeta contexto dos agentes)
3. BAIXA: Lições históricas (informacional — anotar, não reescrever)
4. SKIP: Arquivos auto-gerados (GEMINI.md), backups, git history
```

---

## 8. Integração com Orquestrador — Matriz de Roteamento

### 8.1 Atualização da Tabela de Agentes

Adicionar à tabela existente no `orchestrator.md`:

| Agente | Responsabilidade | Skills |
|--------|------------------|--------|
| **stack-intelligence** | Detecção, validação e sincronização de stack tecnológica | stack-audit |

### 8.2 Atualização do Intent Classification

Adicionar novo intent:

| Intent | Descrição | Indicadores | Agentes | Skill |
|--------|-----------|-------------|---------|-------|
| `stack_management` | Verificação/atualização de stack | "stack", "versão", "upgrade", "dependência" | Stack-Intelligence → Architect | stack-audit |

### 8.3 Consulta Passiva (novo padrão)

Diferente dos outros agentes que são acionados por intent do usuário, o Stack Intelligence também opera **passivamente**:

- Consultado automaticamente no início de sessão
- Consultado antes de gerar artefatos com metadata de stack
- Consultado após detecção de mudança em lock files

---

## 9. Impacto e Benefícios Esperados

### 9.1 Problemas Que Resolve

| Problema | Antes | Depois |
|----------|-------|--------|
| Templates desatualizados | Exemplos hardcoded com versões antigas | Placeholders resolvidos dinamicamente |
| Drift de documentação | Documentos envelhecem silenciosamente | Validação automática no início de sessão |
| Lições sem contexto de versão | "Livewire 3" sem saber se ainda é relevante | Anotação automática pós-upgrade |
| Fragmentação entre sistemas | 6 sistemas com informações diferentes | Centro de verdade único (stack.json) |
| Contexto incorreto para agentes | Agentes recebem "stack: laravel" sem versão | Agentes recebem stack detalhada e verificada |

### 9.2 Métricas de Sucesso

- Zero referências de versão incorretas em novos artefatos gerados
- 100% dos sync_targets validados no início de cada sessão
- Histórico completo de mudanças de stack documentado
- Tempo de detecção de drift: < 1 sessão (imediato no início)

---

## 10. Considerações para Brainstorming

### 10.1 Perguntas Abertas para Discussão

1. **Escopo de detecção**: Deve o agente detectar apenas dependências diretas ou também transitivas (ex: `symfony/http-kernel` via Laravel)?

2. **Granularidade de sync**: Sincronizar apenas major versions (Laravel 12) ou exact versions (Laravel 12.50.0)?

3. **Cross-project**: O Stack Intelligence deveria funcionar cross-projeto (compartilhar padrões entre Spadaer e outros projetos)?

4. **Autonomia**: O agente deveria corrigir inconsistências automaticamente (high confidence) ou sempre pedir aprovação?

5. **Formato de placeholders**: `{{stack.framework}}` é o melhor formato? Ou usar algo mais expressivo como `${stack:backend.framework.name}`?

6. **Backward compatibility**: Como lidar com projetos que não têm `stack.json`? Migration path?

7. **MCP Integration**: Usar Laravel Boost MCP como fonte adicional de runtime info?

### 10.2 Alternativas a Considerar

- **Alternativa A**: Agente completo (proposta principal deste RFC)
- **Alternativa B**: Skill do orquestrador (sem agente dedicado) — mais simples, menos autônomo
- **Alternativa C**: Hook de pré-sessão (script bash) — mais leve, sem inteligência de classificação
- **Alternativa D**: Extensão do State Manager — adicionar responsabilidade ao agente existente

### 10.3 Riscos e Mitigações

| Risco | Probabilidade | Mitigação |
|-------|---------------|-----------|
| Over-engineering para projetos simples | Média | Manter skill opcional, não obrigatória |
| Falsos positivos em detecção de drift | Baixa | Classificação CONTEXTUAL para referências válidas |
| Performance no início de sessão | Baixa | Cache de stack.json com TTL |
| Conflito com auto-generated files | Média | Lista de exclusão (GEMINI.md, backups, .git) |

---

## 11. Próximos Passos Sugeridos

1. **Brainstorming** (skill: brainstorming) — Avaliar viabilidade e escolher entre alternativas A-D
2. **Design** (skill: writing-plans) — Detalhar implementação da alternativa escolhida
3. **Prototipação** — Implementar stack.json + detecção básica como proof of concept
4. **Validação** — Testar no projeto Spadaer (corrigir os 8 arquivos identificados)
5. **Generalização** — Adaptar para funcionar com qualquer stack (não apenas Laravel)

---

## Observations

- [Stack Intelligence Agent] propõe [Detecção automática de stack tecnológica]
- [Stack Intelligence Agent] resolve [Fragmentação de informações de versão entre 6 sistemas AI]
- [Causa Raiz do problema] é [Ausência de agente responsável por rastrear stack]
- [stack.json] serve como [Centro de verdade único para versões]
- [Skill stack-audit] implementa [Fluxo de 5 etapas: Detect → Compare → Validate → Sync → Document]
- [Placeholders dinâmicos] substituem [Exemplos hardcoded em templates]
- [Protocolo Gatekeeper] governa [Sincronização — sempre com aprovação do usuário]
- [Projeto Spadaer] evidencia [8 gargalos de versão em 3 categorias]
