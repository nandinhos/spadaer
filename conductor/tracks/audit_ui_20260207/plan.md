# Implementation Plan - Padronização UI e Auditoria

## Fase 1: Conclusão da Padronização UI (Sincronia aidev)
- [x] Task: Validar e converter botões em Listagens (boxes/index, projects/show) conforme aidev 5.1 (7f3e72b)
- [ ] Task: Validar e converter botões em Páginas de Detalhe (boxes/show, commissions/show, documents/show) conforme aidev 5.2
- [ ] Task: Validar e converter botões em Formulários conforme aidev 5.3
- [ ] Task: Validar e converter botões em Componentes Reutilizáveis conforme aidev 5.4
- [ ] Task: Validar e converter botões em Auth, Profile e Admin conforme aidev 5.5
- [ ] Task: Conductor - User Manual Verification 'Conclusão da Padronização UI' (Protocol in workflow.md)

## Fase 2: Infraestrutura de Auditoria
- [ ] Task: Definir estratégia de Auditoria e configurar migrações/pacote
- [ ] Task: Implementar Log de Ações (CRUD) nos modelos principais via Eloquent Observables ou Trait
- [ ] Task: Criar interface de visualização de logs exclusiva para o Admin Master
- [ ] Task: Conductor - User Manual Verification 'Infraestrutura de Auditoria' (Protocol in workflow.md)

## Fase 3: Segurança e Marcadores de Sigilo
- [ ] Task: Revisar políticas de acesso Spatie para documentos de diferentes graus de sigilo
- [ ] Task: Implementar marcadores visuais (cadeado/tarja) nos componentes Blade de listagem e modal
- [ ] Task: Implementar interceptor de visualização para documentos sigilosos (log de leitura)
- [ ] Task: Conductor - User Manual Verification 'Segurança e Marcadores de Sigilo' (Protocol in workflow.md)
