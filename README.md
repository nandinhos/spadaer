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

O projeto utiliza o **Laravel Sail** para garantir um ambiente padronizado.

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
