import { test, expect } from '@playwright/test';
import fs from 'node:fs';
import path from 'node:path';

test('document CSV import succeeds', async ({ page }) => {
    const stamp = Date.now().toString().slice(-6);
    const item = 100 + (Number(stamp) % 800);
    const csv = [
        'box_id,project_id,item_number,document_number,title,document_date',
        `6,1,${item},DOC-E2E-${stamp},Documento E2E ${stamp},09/2026`,
    ].join('\n');
    const fixture = path.resolve('tests/e2e/fixtures/import-e2e.csv');
    fs.mkdirSync(path.dirname(fixture), { recursive: true });
    fs.writeFileSync(fixture, csv);

    await page.goto('/documents');
    await page.locator('#csv_file').setInputFiles(fixture);

    await expect(page.getByText(/documentos importados com sucesso/i).first()).toBeVisible();
    await expect(page.getByText(`DOC-E2E-${stamp}`)).toBeVisible();
});
