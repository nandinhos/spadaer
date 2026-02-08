# Lições Aprendidas - Refatoração de Gestão de Usuários e Papéis

## 1. Livewire 3: Estratégia de Hidratação
- **Problema:** Erro `getMorphClass` ao tentar serializar Coleções Eloquent complexas em propriedades públicas.
- **Solução:** Evitar armazenar Coleções Eloquent em propriedades públicas se elas não precisarem ser persistidas entre requests de forma mutável. Preferir buscar os dados diretamente no método `render()`. Isso resolve erros de síntese do Livewire 3 e melhora a performance (menos dados trafegados no payload).

## 2. UI/UX para Permissões Complexas
- **Escalabilidade:** Para telas com muitas permissões, modais tornam-se limitados. A transição para páginas dedicadas (`RoleEdit`) com categorias bem definidas (Matriz de Competências) oferece uma experiência mais organizada e profissional.
- **Feedback Visual:** Uso de badges semânticos com ícones e cores específicas facilita a identificação rápida de papéis de alto privilégio.

## 3. Segurança e Integridade
- **Proteção de Papéis Base:** Papéis como `admin` e `user` devem ter travas de rename/delete em múltiplos níveis:
    - Frontend: Desabilitar inputs e remover botões de exclusão.
    - Backend: Validar no método de salvamento/exclusão (ex: `RoleEdit::save` e `RoleManager::deleteRole`).

## 4. Testes como Ferramenta de Depuração
- A criação de testes de feature específicos para componentes Livewire (`RoleManagerTest`, `RoleEditTest`) é a melhor forma de validar correções de bugs de ciclo de vida e garantir que novas funcionalidades não quebrem fluxos críticos.
