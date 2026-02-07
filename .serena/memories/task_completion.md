# Task Completion Workflow

Before considering a task finished, ensure the following steps are taken:

1. **Verify with Tests**:
   - Run the full test suite: `./vendor/bin/sail php artisan test`.
   - Ensure the specific tests for your changes pass.

2. **Code Formatting**:
   - Run Laravel Pint to ensure code follows the project's style: `./vendor/bin/sail vendor/bin/pint`.

3. **Git Commit**:
   - Create atomic commits.
   - Use Portuguese for messages.
   - Follow the format: `tipo(escopo): descricao`.

4. **Verify Standards**:
   - Check if the changes adhere to the "Laravel Stack Rules" (thin controllers, logic in services, etc.).
