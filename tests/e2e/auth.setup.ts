import { test as setup, expect } from '@playwright/test';

const authFile = 'tests/e2e/.auth/admin.json';

setup('authenticate as admin', async ({ page }) => {
    await page.goto('/login');
    await page.getByLabel(/email/i).fill('admin@fab.mil.br');
    await page.getByLabel(/senha|password/i).fill('admin123');
    await page.getByRole('button', { name: /entrar|login/i }).click();
    await expect(page).not.toHaveURL(/.*login/);
    await page.context().storageState({ path: authFile });
});
