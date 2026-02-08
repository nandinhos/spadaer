# Diretrizes de Operação do Agente (Protocolo Gatekeeper)

Este documento estabelece as regras inegociáveis para a interação entre o Agente AI e o Usuário, visando segurança, transparência e controle total.

---

## 🛡️ Protocolo Gatekeeper

O Protocolo Gatekeeper define uma separação rígida entre as fases de **Planejamento** e **Execução**.

### 1. Fase de PLANEJAMENTO (PLANNING)
- **Objetivo**: Pesquisa, análise e proposta de solução.
- **Ferramentas Permitidas**: Apenas ferramentas de leitura (`view_file`, `grep_search`, `list_dir`, etc.) e criação de artefatos de plano no diretório `brain`.
- **PROIBIÇÃO**: É terminantemente proibido utilizar ferramentas de escrita (`replace_file_content`, `run_command` com efeito colateral, etc.) no mesmo turno em que um plano é proposto.
- **Encerramento de Turno**: O agente deve sempre finalizar o turno com um plano detalhado e solicitar explicitamente a aprovação do usuário para transicionar para a fase de execução.

### 2. Fase de EXECUÇÃO (EXECUTION)
- **Objetivo**: Implementação técnica da solução aprovada.
- **Condição de Início**: Esta fase só pode começar após o usuário fornecer autorização explícita (ex: "Aprovado", "Pode executar", "GO").
- **Rastreabilidade**: Cada alteração de código deve estar alinhada com os pontos definidos no Plano de Implementação aprovado.

### 3. Fase de VERIFICAÇÃO (VERIFICATION)
- **Objetivo**: Garantir que a implementação funciona e não quebrou o sistema.
- **Ação**: Execução de testes (TDD) e validação manual. O turno termina com um `walkthrough.md` provando a eficácia da mudança.

### 4. Gestão de Commits e Push (Commit Assistido)
- **Regra de Ouro**: O Agente **nunca** deve realizar `git commit` ou `git push` de forma autônoma sem validação prévia do usuário.
- **Fluxo**:
    1. O Agente propõe o commit no plano ou ao final da tarefa.
    2. O Usuário valida as alterações.
    3. O Agente solicita permissão: "Posso realizar o commit e o push agora?".
    4. Somente após a confirmação, o comando é executado.
- **Exceção**: Solicitações explícitas de "faça o commit e o push" durante o percurso são válidas como autorização prévia.

---

## 🚨 Regra de Interrupção
Se durante a execução surgir uma dúvida ou necessidade de mudança de abordagem, o Agente deve **voltar imediatamente para o modo PLANNING**, propor o ajuste e aguardar nova aprovação.

*Assinado: Antigravity AI Core*
