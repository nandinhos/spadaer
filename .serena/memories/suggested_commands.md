# Suggested Commands for spadaer

## Docker / Sail
- Start project: `./vendor/bin/sail up -d`
- Stop project: `./vendor/bin/sail stop`
- Run command inside container: `./vendor/bin/sail [command]` (e.g., `./vendor/bin/sail php artisan migrate`)

## Artisan (via Sail)
- Run tests: `./vendor/bin/sail php artisan test`
- Run specific test: `./vendor/bin/sail php artisan test --filter=ClassName`
- Migration status: `./vendor/bin/sail php artisan migrate:status`
- Migrate: `./vendor/bin/sail php artisan migrate`
- Seed database: `./vendor/bin/sail php artisan db:seed`
- Route list: `./vendor/bin/sail php artisan route:list`
- Clear cache: `./vendor/bin/sail php artisan optimize:clear`

## Frontend / NPM
- Development: `npm run dev`
- Build: `npm run build`

## Code Quality
- Format code (Pint): `./vendor/bin/sail vendor/bin/pint`

## AI Dev Superpowers
- Activate agent: `aidev agent` (or triggers like "modo agente")
- Check environment: `aidev doctor`
