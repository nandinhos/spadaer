# Licao: Gerenciamento de Links de Storage em Ambientes Docker/Sail

**Data**: 2026-02-07
**Stack**: Laravel, Docker, Sail
**Tags**: success-pattern, config, deployment, storage

## Contexto
O Laravel utiliza links simbólicos para expor arquivos privados (`storage/app/public`) na pasta pública (`public/storage`).

## Problema
Arquivos (como PDFs de portarias) retornavam erro `403 Forbidden` ou `404 Not Found` mesmo existindo no disco.

## Causa Raiz
O link simbólico foi criado fora do container ou aponta para um caminho absoluto do host. No Docker, o link deve apontar para a estrutura interna do container (`/var/www/html/storage/...`).

## Solução
Executar o comando de linkagem obrigatoriamente através do Sail (dentro do container):

```bash
vendor/bin/sail artisan storage:link
```

Isso garante que o caminho apontado pelo link seja `/var/www/html/storage/app/public`, que é o que o servidor web (Nginx/Apache) dentro do container consegue resolver.

## Prevenção
- Adicionar o comando `php artisan storage:link` em scripts de setup/deploy automatizados.
- Se o erro 403 persistir, verificar as permissões das pastas dentro do container (devem pertencer ao usuário `safely` ou `www-data`, dependendo da imagem).
