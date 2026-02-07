# Licao: Configuração do MCP Laravel Boost via Docker Compose (Sail)

**Data**: 2026-02-07
**Stack**: Laravel 12, Sail, Docker, Gemini CLI
**Tags**: success-pattern, config, mcp, docker, laravel-boost

## Contexto
O projeto utiliza Laravel Sail para o ambiente de desenvolvimento. Ao tentar utilizar o MCP `laravel-boost`, era necessário que ele tivesse acesso ao contexto da aplicação (banco de dados, comandos Artisan, etc.).

## Problema
Executar o comando `php artisan boost:mcp` localmente (fora do container) falha pois as dependências e o banco de dados estão isolados no Docker. O Gemini CLI precisa de uma forma de invocar o MCP que garanta a execução dentro do container `laravel.test`.

## Causa Raiz
O MCP `laravel-boost` depende do ambiente Laravel funcional. Em setups com Sail, o ambiente só existe plenamente dentro do container Docker.

## Solução
A configuração do servidor MCP no arquivo `.mcp.json` (ou na configuração global do Gemini CLI) deve usar o `docker compose exec` para "entrar" no container e executar o comando.

**Configuração Aplicada (`.mcp.json`):**
```json
{
  "mcpServers": {
    "laravel-boost": {
      "command": "docker",
      "args": [
        "compose",
        "exec",
        "-T",
        "laravel.test",
        "php",
        "artisan",
        "boost:mcp"
      ],
      "env": {
        "WWWUSER": "1000",
        "WWWGROUP": "1000"
      }
    }
  }
}
```

### Por Que Funciona
- `docker compose exec` executa o comando dentro do container já rodando.
- `-T` desabilita a alocação de pseudo-TTY, necessário para integração com processos que capturam stdout/stdin (como o MCP host).
- `laravel.test` é o nome padrão do serviço no Sail.
- `WWWUSER`/`WWWGROUP` garantem que o comando rode com as permissões corretas do usuário do host mapeado no container.

## Prevenção
- Sempre que um MCP precisar interagir com o ecossistema Laravel em projetos Sail, prefira a execução via `docker compose exec`.
- Certifique-se de que o container está rodando (`sail up`) antes de usar o MCP.
