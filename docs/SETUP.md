# Setup do Projeto SPADAER

> Guia passo a passo para configurar o projeto em um novo ambiente de desenvolvimento.

---

## 📋 Pré-requisitos

- **PHP** >= 8.4
- **Composer** (gerenciador de dependências PHP)
- **Node.js** >= 18.x
- **NPM** ou **Yarn**
- **Git**
- **Banco de Dados**: SQLite (padrão) ou MySQL/PostgreSQL

### Opcional (Recomendado)
- **Laravel Sail** (Docker) - para ambiente containerizado

---

## 🚀 Instalação Rápida

### 1. Clone o Repositório

```bash
git clone https://github.com/nandinhos/spadaer.git
cd spadaer
```

---

### 2. Configuração do Ambiente

#### 2.1 Arquivo de Configuração (.env)

```bash
# Copie o arquivo de exemplo
cp .env.example .env

# Gere a chave da aplicação
php artisan key:generate
```

#### 2.2 Configurar Banco de Dados

**Opção A: SQLite (Mais Simples - Padrão)**
```bash
# Crie o arquivo do banco de dados
touch database/database.sqlite

# O .env.example já vem configurado para SQLite:
# DB_CONNECTION=sqlite
```

**Opção B: MySQL**
```bash
# Edite o arquivo .env e configure:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spadaer
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

**Opção C: PostgreSQL**
```bash
# Edite o arquivo .env e configure:
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=spadaer
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

---

### 3. Instalação de Dependências

Para ambientes onde o PHP/Composer não está instalado no host (como o atual), utilize o **Bootstrap via Docker**:

#### 3.1 PHP (Composer via Docker)

Execute este comando para instalar as dependências do Laravel e do Sail sem precisar de PHP no host:

```bash
docker run --rm \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    --user "$(id -u):$(id -g)" \
    composer:latest composer install --ignore-platform-reqs
```

#### 3.2 JavaScript/Node (NPM via Sail)

Após o passo anterior, o Sail estará disponível no diretório `vendor`. Suba os containers e use-o para instalar as dependências de frontend:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

---

### 4. Configuração do Banco de Dados

#### 4.1 Executar Migrations

```bash
php artisan migrate
```

#### 4.2 Popular com Dados Iniciais (Seeders)

```bash
php artisan db:seed
```

Ou tudo de uma vez:
```bash
php artisan migrate --seed
```

#### 4.3 Criar Usuário Admin (Opcional)

```bash
php artisan tinker
```

No prompt do Tinker, execute:
```php
$user = App\Models\User::factory()->create([
    'name' => 'Admin',
    'email' => 'admin@spadaer.com',
    'password' => bcrypt('senha123')
]);

// Atribuir todas as permissões
$user->assignRole('admin');
```

---

### 5. Configurações Finais

#### 5.1 Link Simbólico do Storage

```bash
php artisan storage:link
```

#### 5.2 Cache de Configurações (Produção)

```bash
# Apenas para ambiente de produção
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Para desenvolvimento, **não execute** os comandos acima (dificulta debug).

---

### 6. Iniciar o Servidor

#### Opção A: PHP Built-in Server

```bash
php artisan serve
```
Acesse: http://localhost:8902

#### Opção B: Laravel Sail (Docker) - RECOMENDADO

Se você não tem PHP instalado localmente, siga estes passos exatos:

1. **Bootstrap inicial**:
```bash
docker run --rm -v $(pwd):/var/www/html -w /var/www/html --user "$(id -u):$(id -g)" composer:latest composer install --ignore-platform-reqs
```

2. **Iniciar containers**:
```bash
./vendor/bin/sail up -d
```

3. **Configuração final**:
```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
./vendor/bin/sail artisan migrate --seed
```
Acesse: http://localhost:8902

#### Opção C: Ambiente Customizado

Configure seu servidor web (Apache/Nginx) apontando para a pasta `public/`.

---

## 🔧 Configurações Específicas

### Permissões de Diretórios (Linux/Mac)

```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### Configuração de Email (Opcional)

Edite `.env` para testar envio de emails:
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=seu_usuario_mailtrap
MAIL_PASSWORD=sua_senha_mailtrap
MAIL_ENCRYPTION=null
```

---

## ✅ Verificação da Instalação

Após completar os passos acima, verifique:

1. **Homepage**: http://localhost:8902 → Deve carregar sem erros
2. **Login**: Tente logar com o usuário admin criado
3. **Listagem de Caixas**: `/boxes` → Deve mostrar a tabela com filtros funcionando
4. **Deleção**: Tente deletar uma caixa (deve funcionar via modal)

---

## 🐛 Troubleshooting

### Erro: "Failed to open stream: Permission denied"
```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### Erro: "SQLSTATE[HY000]: General error: 8 attempt to write a readonly database" (SQLite)
```bash
chmod 666 database/database.sqlite
chmod 775 database/
```

### Erro: "Vite manifest not found"
```bash
npm install
npm run build
```

### Erro: "Class not found" ou "Trait not found"
```bash
composer dump-autoload
```

### Erro: "Route not found"
```bash
php artisan route:clear
```

### Erro: "View not found"
```bash
php artisan view:clear
```

---

## 📝 Notas Importantes

### Arquivos Não Versionados (Git)

Estes arquivos serão criados localmente e **não** devem ser commitados:

- `.env` - Contém credenciais e configurações sensíveis
- `.env.*` - Outros ambientes
- `database/database.sqlite` - Banco de dados SQLite
- `storage/` - Logs, cache, uploads (exceto estrutura)
- `vendor/` - Dependências PHP
- `node_modules/` - Dependências Node.js
- `public/build/` - Assets compilados (gerado por `npm run build`)
- `public/storage/` - Link simbólico (gerado por `artisan storage:link`)

### AI Dev Superpowers

O projeto usa **AI Dev Superpowers** para governança de desenvolvimento. Os arquivos de configuração estão em:

```
.aidev/
├── AI_INSTRUCTIONS.md      # Instruções gerais
├── AGENT_PROTOCOLS.md      # Protocolos de operação
├── agents/                  # Definições de agentes
├── plans/                   # Roadmaps e planos
│   ├── ROADMAP.md
│   └── box-list-livewire-conversion.md
├── rules/                   # Regras por stack
│   └── laravel.md
└── state/                   # Estado da sessão (não versionado)
```

Para ativar o modo agente:
```bash
aidev agent
```

---

## 🔄 Comandos Úteis

### Desenvolvimento
```bash
# Watch mode (rebuild automático)
npm run dev

# Testes
php artisan test

# Code style
./vendor/bin/pint

# Análise estática
./vendor/bin/phpstan analyse
```

### Manutenção
```bash
# Limpar caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Otimizar (produção)
php artisan optimize

# Reset completo do banco
php artisan migrate:fresh --seed
```

---

## 📞 Suporte

Se encontrar problemas:

1. Verifique logs em `storage/logs/laravel.log`
2. Consulte documentação: `.aidev/QUICKSTART.md`
3. Ative modo debug: `APP_DEBUG=true` no `.env`

---

**Última atualização:** 2026-02-09  
**Versão do Laravel:** 12.x  
**Versão do PHP:** >= 8.4
