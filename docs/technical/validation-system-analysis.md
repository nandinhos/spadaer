# Plano de Análise: Sistema de Validação Robusta (Backup)

Este documento detalha o funcionamento técnico do sistema de validação, conforme solicitado.

## 1. Visão Geral
O sistema utiliza uma abordagem de **múltiplas camadas** para garantir que nenhum dado inválido penetre na base de dados, especialmente durante importações CSV.

## 2. Pontos Detalhados do Sistema

### A. Camada de Normalização (Sanitização)
Antes de validar, o sistema "limpa" os dados:
- **Datas**: Processadas via `validateAndNormalizeMonthYear`, garantindo o padrão `MM/YYYY`.
- **Case Inensitivity**: Cabeçalhos do CSV são convertidos para minúsculas antes do mapeamento, evitando erros por "Título" vs "título".

### B. Camada de Validação de Esquema (Laravel Validator)
- **Campos Obrigatórios**: `item_number`, `document_number`, `title` e `document_date`.
- **Integridade Referencial**: Valida se `box_id` e `project_id` existem no banco (`exists:table,id`).

### C. Camada de Unicidade Robusta (Lógica Avançada)
Aqui está o diferencial do seu sistema (localizado no `Validator::after` das classes de Import):
1. **Unicidade Composta**: O sistema não olha apenas para o número do documento. Ele valida o par `document_number` + `is_copy`. Isso permite gerenciar versões/cópias sem duplicar o registro base.
2. **Contexto de Caixa**: Garante que o `item_number` seja único **dentro daquela caixa específica**, mas permite o mesmo número em caixas diferentes.

### D. Camada de Transação e Feedback
- **Transação Atômica**: O `DocumentImportController` utiliza `DB::beginTransaction()`. Se uma única linha falhar na inserção (mesmo após passar na validação), o sistema reverte tudo.
- **Relatório de Erros**: Coleta as falhas linha por linha e as exibe com os dados originais, facilitando a correção pelo usuário.

---

## 3. Próximos Passos Sugeridos
Após sua revisão deste plano, podemos:
1. **Refatorar para Service**: Centralizar essa lógica robusta em um `DocumentValidationService`.
2. **Segurança Fase 3**: Integrar verificações de Nível de Sigilo nesta mesma camada de validação.
