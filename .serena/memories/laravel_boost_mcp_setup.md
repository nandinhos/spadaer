# Configuração do MCP Laravel Boost (Docker/Sail)

Para que o MCP Laravel Boost funcione corretamente em projetos utilizando Laravel Sail, ele deve ser executado dentro do container Docker.

## Configuração do .mcp.json

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

## Pontos Chave:
- **docker compose exec**: Executa no container ativo.
- **-T**: Remove a necessidade de TTY (essencial para MCP).
- **laravel.test**: Serviço padrão do Sail.
- **Ambiente**: Garante acesso ao banco de dados e Artisan.