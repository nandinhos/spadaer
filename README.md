# SPADAER 🛡️

**Sistema de Processamento e Arquivo de Documentos Administrativos e Eletrônicos Registrados**

O SPADAER é uma plataforma corporativa "Premium" de alta performance projetada para a gestão integral de acervos documentais, projetos administrativos e comissões. Construído com as tecnologias mais modernas do ecossistema PHP, o sistema prioriza a segurança, integridade de dados e uma experiência de usuário fluida.

---

## ✨ Funcionalidades em Destaque

### 💎 Interface Premium & UX
- **Design Moderno**: Interface construída com **Glassmorphism**, tipografia **Outfit** e micro-animações.
- **Dark Mode Nativo**: Suporte total ao tema escuro com persistência via LocalStorage e prevenção de *layout shift*.
- **Sidebar Inteligente**: Navegação colapsável que respeita o estado do usuário entre as páginas.

### 🛡️ Segurança e Governança
- **Auditoria Polimórfica Automatizada**: Todas as ações de criação, atualização e exclusão em Documentos, Caixas, Projetos e Comissões são registradas automaticamente.
- **Gestão de Permissões**: Controle de acesso baseado em funções (Admin, Presidente de Comissão, Usuário) via Spatie Permissions.
- **Logs de Admin Master**: Interface dedicada para visualização de trilhas de auditoria.

### 📊 Gestão de Acervo & Importação
- **Validação Robusta de Entrada**: Sistema inteligente de importação CSV que realiza normalização de datas, validação de unicidade composta (ex: Número + Cópia) e fornece relatórios detalhados de erros por linha.
- **Organização Hierárquica**: Vínculo entre Projetos > Comissões > Caixas (Boxes) > Documentos.
- **Exportação Multiformato**: Geração de relatórios e listagens em Excel (CSV) e PDF.

---

## 🏗️ Organização Hierárquica do Acervo

O SPADAER organiza o acervo documental em uma estrutura hierárquica de quatro níveis:

```
Projeto (Project)
└── Comissão (Commission)
    └── Caixa (Box)
        └── Documento (Document)
```

| Nível | Descrição | Exemplo |
|---|---|---|
| **Projeto** | Agrupamento administrativo de alto nível. Possui nome, código único e descrição. | `PRJ-2024 — Inventário Anual` |
| **Comissão** | Grupo de trabalho constituído por portaria, com presidente, membros e secretário. Responsável pela conferência do acervo. | `Comissão Permanente de Avaliação` |
| **Caixa** | Unidade física de armazenamento. Possui numeração sequencial, localização física e conferente designado. | `CX001 — Prateleira A, Bloco 2` |
| **Documento** | Registro documental individual com metadados completos: número, título, data, sigilo, versão e indicação de cópia. | `OF 123/2024 — Confidencial` |

---

## 📄 Gestão de Documentos

### Metadados do Documento

Cada documento registrado no sistema possui os seguintes atributos:

| Atributo | Descrição |
|---|---|
| **Número do Documento** | Identificador único (ex: `OF 123/2024`) |
| **Título** | Descrição do conteúdo |
| **Número do Item** | Sequencial dentro da caixa |
| **Data** | Data do documento (formato `MM/AAAA`) |
| **Nível de Sigilo** | Classificação de acesso (Ostensivo, Restrito, Confidencial, Secreto) |
| **Código** | Código de classificação documental |
| **Descritor** | Descritor temático para categorização |
| **Versão** | Controle de versionamento |
| **Cópia** | Indicação se o registro é original ou cópia |

### Criação Manual

Documentos podem ser criados individualmente via formulário, com validação de campos obrigatórios, unicidade composta (número + cópia) e verificação de item único por caixa.

### Importação em Lote (CSV)

O sistema realiza importação massiva de documentos com um pipeline robusto de validação:

1. **Upload**: Aceita arquivos CSV/TXT (máx. 5 MB)
2. **Mapeamento**: Leitura e mapeamento automático de colunas
3. **Validação linha por linha**: Campos obrigatórios, tipos, normalização de datas (`MM/AAAA`), verificação de sigilo válido, unicidade composta
4. **Relatório de erros**: Se houver erros, retorna lista detalhada (linha, campo, mensagem, valores encontrados) **sem inserir nenhum registro**
5. **Inserção transacional**: Se todos os dados forem válidos, insere em transação atômica (tudo ou nada)

### Exportação

- **Excel (.xlsx)**: Exportação de listagens com filtros preservados via `Maatwebsite/Excel`
- **PDF**: Geração de relatórios formatados via `Barryvdh/DomPDF`

---

## 📦 Sistema de Caixas e Armazenagem

As caixas representam as **unidades físicas de armazenamento** do acervo.

### Funcionalidades

| Funcionalidade | Descrição |
|---|---|
| **Numeração Sequencial** | Criação automática com incremento (ex: `CX001`, `CX002`, ..., `CX200`) |
| **Criação em Lote** | Até 200 caixas de uma vez com numeração sequencial automática |
| **Localização Física** | Campo descritivo para posição no arquivo (ex: `Prateleira A, Bloco 2`) |
| **Conferente** | Membro de comissão designado como responsável pela conferência |
| **Data de Conferência** | Registro de quando a caixa foi conferida |
| **Importação por Caixa** | Importação de documentos diretamente vinculados a uma caixa específica |
| **Filtros Avançados** | Busca por número, localização, projeto, conferente e status (com documentos / vazias) |
| **Exclusão Inteligente** | Caixas vazias são excluídas; caixas com documentos têm seus docs desassociados (não excluídos) |

---

## 👥 Comissões e Membros

O módulo de comissões gerencia os **grupos de trabalho** responsáveis pela conferência e validação do acervo.

### Estrutura da Comissão

| Campo | Descrição |
|---|---|
| **Nome** | Identificação da comissão |
| **Descrição** | Objetivo e escopo de atuação |
| **Status** | Ativo ou Inativo |
| **Número da Portaria** | Ato normativo de constituição |
| **Data da Portaria** | Data de publicação |
| **Arquivo da Portaria** | Documento PDF/DOC anexado |

### Papéis dos Membros

| Papel | Responsabilidade |
|---|---|
| **Presidente** | Coordena os trabalhos da comissão. Pode criar e editar documentos. |
| **Membro** | Participa das atividades de conferência. Pode visualizar e criar documentos. |
| **Secretário** | Suporte administrativo à comissão. |

### Vínculo Operacional

- Membros de comissão podem ser designados como **conferentes de caixas**, vinculando a comissão diretamente à atividade de verificação física do acervo.
- A gestão de membros (adição/remoção) calcula automaticamente o *diff* entre a composição atual e a desejada.

---

## 🔐 Níveis de Sigilo

O SPADAER implementa um sistema de classificação de sigilo para documentos:

| Nível | Acesso |
|---|---|
| **Ostensivo / Público** | Livre para qualquer usuário com permissão `documents.view` |
| **Restrito** | Requer permissão especial `documents.view.secret` |
| **Confidencial** | Requer permissão especial `documents.view.secret` |
| **Secreto** | Requer permissão especial `documents.view.secret` |

### Fluxo de Verificação

```
Usuário acessa documento
        │
        ▼
   Tem permissão documents.view? ── NÃO ──► BLOQUEADO
        │
       SIM
        ▼
   Documento é sigiloso? ── NÃO ──► ACESSO LIBERADO
        │
       SIM
        ▼
   Tem permissão documents.view.secret? ── NÃO ──► BLOQUEADO
        │
       SIM ──► ACESSO LIBERADO
```

Por padrão, apenas o papel **Administrador** possui a permissão `documents.view.secret`.

---

## 🔍 Rastreio e Auditoria

### Auditoria Automatizada

O sistema registra automaticamente **todas as operações** nos modelos principais através do trait `Auditable`:

| Evento | Dados Capturados |
|---|---|
| **Criação** | Todos os atributos do novo registro |
| **Edição** | Valores anteriores e novos (apenas campos alterados) |
| **Exclusão** | Todos os atributos do registro excluído |
| **Visualização** | Registro de acesso ao documento |

Cada log de auditoria inclui: **usuário responsável**, **endereço IP**, **user-agent do navegador** e **timestamp**.

### Modelos Auditados

- Documentos (`Document`)
- Caixas (`Box`)
- Comissões (`Commission`)
- Projetos (`Project`)

### Revisão de Documentos

Além da auditoria automática, o sistema mantém um registro específico de **revisões por documento** (`DocumentReview`), permitindo rastrear quem revisou cada documento e quando, com espaço para observações.

### Painel de Auditoria

Interface administrativa dedicada (`/admin/audit`) para consulta de trilhas de auditoria com filtros por modelo, usuário e tipo de evento.

---

## 🛂 Controle de Acesso (RBAC)

O controle de acesso é baseado em papéis (Role-Based Access Control) com 4 níveis de autorização: Middleware, Gates, Policies e verificações em componentes.

### Papéis do Sistema

| Papel | Capacidades Principais |
|---|---|
| **Administrador** | Acesso total: gestão de usuários, documentos, caixas, comissões, projetos, sigilo e auditoria |
| **Presidente de Comissão** | Criar/editar documentos, editar comissões, exportar (Excel/PDF) |
| **Membro de Comissão** | Visualizar e criar documentos, exportar (Excel/PDF) |
| **Usuário** | Visualizar documentos e comissões, gerenciar caixas |

### Permissões Granulares

O sistema define **20 permissões** organizadas em 5 módulos: Usuários, Documentos, Comissões, Caixas e Projetos. As permissões podem ser atribuídas por papel ou diretamente a usuários individuais através do painel administrativo.

---

## 🛠️ Stack Tecnológica

- **Core**: [Laravel 12](https://laravel.com) (PHP 8.4+)
- **Frontend**: [Livewire 4](https://livewire.laravel.com), [Alpine.js](https://alpinejs.dev) & [Tailwind CSS](https://tailwindcss.com)
- **Banco de Dados**: MySQL 8.0
- **Integrações**:
  - `Maatwebsite/Excel` (Importação/Exportação)
  - `Barryvdh/DomPDF` (Relatórios PDF)
  - `Spatie/Laravel-Permission` (RBAC)
- **Infraestrutura**: Docker via [Laravel Sail](https://laravel.com/docs/sail)

---

## 🚀 Instalação e Setup

O projeto utiliza o **Laravel Sail** para garantir um ambiente padronizado. Para um guia de instalação detalhado com passo a passo completo e configurações específicas, consulte o **[Guia de Setup Oficial (docs/SETUP.md)](docs/SETUP.md)**.


1. **Clone o repositório**:
   ```bash
   git clone https://github.com/nandinhos/spadaer.git
   cd spadaer
   ```

2. **Instale as dependências**:
   ```bash
   docker run --rm \
       -u "$(id -u):$(id -g)" \
       -v "$(pwd):/var/www/html" \
       -w /var/www/html \
       laravelsail/php84-composer:latest \
       composer install --ignore-platform-reqs
   ```

3. **Inicie os containers**:
   ```bash
   ./vendor/bin/sail up -d
   ```

4. **Prepare a aplicação**:
   ```bash
   ./vendor/bin/sail artisan key:generate
   ./vendor/bin/sail artisan migrate --seed
   ./vendor/bin/sail npm install
   ./vendor/bin/sail npm run build
   ```

---

## 🤖 AI Development Flow

Este projeto é desenvolvido com o auxílio de agentes de IA avançados. Para manter o contexto e a integridade:

- **CLI AI Dev**: O comando `aidev` gerencia as ativações e o estado do projeto.
- **Sincronização**: O script `./.aidev/scripts/sync-state.sh` garante que o `ROADMAP.md` e os arquivos de estado da IA estejam sempre alinhados.
- **Documentação Técnica**: Detalhes sobre arquitetura, KB e decisões de design residem em `docs/technical/`.

---

## 📄 Licença

O SPADAER é um software sob a licença [MIT](https://opensource.org/licenses/MIT).
