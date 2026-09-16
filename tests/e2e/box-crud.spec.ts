import { test, expect } from '@playwright/test';

test('boxes list loads and search filters', async ({ page }) => {
    await page.goto('/boxes');
    await expect(page.getByRole('heading', { name: /caixas/i })).toBeVisible();

    const stamp = Date.now().toString().slice(-6);
    const number = `CX-E2E-${stamp}`;

    await page.goto('/boxes/create');
    await page.locator('#number').fill(number);
    await page.locator('#physical_location').fill('Galpão E2E');
    await page.locator('#project_id').selectOption({ index: 1 });
    await page.getByRole('button', { name: /salvar|criar|cadastrar/i }).click();

    await expect(page).toHaveURL(/.*boxes.*/);
    await page.goto('/boxes');
    await page.locator('input[type="search"], input[placeholder*="usc" i]').first().fill(number);
    await expect(page.getByText(number)).toBeVisible();
});
