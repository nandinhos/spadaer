# Style and Conventions for spadaer

## General Principles
- **TDD is Mandatory**: Write a failing test before implementing any feature or fix.
- **YAGNI & DRY**: Only implement what is requested; avoid code repetition.
- **Clean Code**: Meaningful names, small functions, single responsibility.

## Code Structure (Laravel)
- **Controllers**: Thin controllers. Use Form Requests for validation.
- **Models**: Singular names. Use relationships and scopes.
- **Services/Actions**: Put business logic here. Use dependency injection.
- **Testing**: Feature tests for endpoints, Unit tests for logic.

## Naming Conventions
- Controllers: `UserController`
- Models: `User`
- Services: `UserService`
- Actions: `CreateUser`
- Requests: `StoreUserRequest`

## Git and Commits
- **Language**: Portuguese (PT-BR) is OBLIGATORY.
- **Emojis**: PROHIBITED.
- **Co-authorship**: PROHIBITED (no `Co-Authored-By`).
- **Format**: `tipo(escopo): descricao em portugues`
  - Types: `feat`, `fix`, `refactor`, `test`, `docs`, `chore`.

## Routing
- Define specific routes (e.g., `/export`) BEFORE generic routes with parameters (e.g., `/{document}`).
- Use route names: `->name('documents.index')`.
