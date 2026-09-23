import fs from 'node:fs/promises';
import path from 'node:path';
import process from 'node:process';
import { chromium } from 'playwright';

const baseUrl = process.env.BRIITE_BASE_URL || 'http://127.0.0.1:8080';
const featuredPostId = process.env.BRIITE_FEATURED_POST_ID;

if (!featuredPostId) {
  throw new Error('BRIITE_FEATURED_POST_ID is required. Run tools/seed-documentation-site.sh first.');
}

const outputDir = path.resolve('docs/screenshots');
await fs.mkdir(outputDir, { recursive: true });

const browser = await chromium.launch({ headless: true });

async function waitForImages(page) {
  await page.evaluate(async () => {
    const images = Array.from(document.images);
    await Promise.all(
      images.map((image) => {
        if (image.complete) {
          return Promise.resolve();
        }
        return new Promise((resolve) => {
          image.addEventListener('load', resolve, { once: true });
          image.addEventListener('error', resolve, { once: true });
        });
      }),
    );
  });
}

async function capture({
  name,
  route,
  viewport,
  expectedStatus = 200,
  fullPage = true,
  beforeCapture,
}) {
  const context = await browser.newContext({
    viewport,
    deviceScaleFactor: 1,
    colorScheme: 'light',
    reducedMotion: 'reduce',
  });
  const page = await context.newPage();
  const runtimeErrors = [];

  page.on('pageerror', (error) => runtimeErrors.push(`pageerror: ${error.message}`));
  page.on('console', (message) => {
    if (message.type() !== 'error') {
      return;
    }

    const text = message.text();
    const expectedNotFoundResourceMessage =
      expectedStatus === 404 &&
      text.includes('Failed to load resource') &&
      text.includes('404');

    if (!expectedNotFoundResourceMessage) {
      runtimeErrors.push(`console: ${text}`);
    }
  });

  const response = await page.goto(`${baseUrl}${route}`, {
    waitUntil: 'networkidle',
    timeout: 60_000,
  });

  if (!response) {
    throw new Error(`No HTTP response while capturing ${name}.`);
  }

  if (response.status() !== expectedStatus) {
    throw new Error(
      `${name} returned HTTP ${response.status()}, expected ${expectedStatus}.`,
    );
  }

  await waitForImages(page);

  if (beforeCapture) {
    await beforeCapture(page);
    await page.waitForTimeout(250);
  }

  await page.screenshot({
    path: path.join(outputDir, name),
    fullPage,
  });

  if (runtimeErrors.length > 0) {
    throw new Error(`${name} produced browser runtime errors:\n${runtimeErrors.join('\n')}`);
  }

  await context.close();
}

try {
  await capture({
    name: 'briite-home-desktop.png',
    route: '/',
    viewport: { width: 1440, height: 1000 },
    beforeCapture: async (page) => {
      const firstWork = page.locator('.work a').first();
      await firstWork.waitFor({ state: 'visible' });
      await firstWork.hover();
    },
  });

  await capture({
    name: 'briite-single-project.png',
    route: `/?p=${featuredPostId}`,
    viewport: { width: 1440, height: 1000 },
  });

  await capture({
    name: 'briite-mobile-navigation.png',
    route: '/',
    viewport: { width: 390, height: 844 },
    beforeCapture: async (page) => {
      const menuButton = page.locator('#menu_icon');
      await menuButton.waitFor({ state: 'visible' });
      await menuButton.click();
      await page.locator('#primary-menu.show_menu').waitFor({ state: 'visible' });
    },
  });

  await capture({
    name: 'briite-404-recovery.png',
    route: '/?p=999999',
    viewport: { width: 1440, height: 900 },
    expectedStatus: 404,
  });

  const directoryContext = await browser.newContext({
    viewport: { width: 1200, height: 900 },
    deviceScaleFactor: 1,
    colorScheme: 'light',
    reducedMotion: 'reduce',
  });
  const directoryPage = await directoryContext.newPage();
  const directoryErrors = [];
  directoryPage.on('pageerror', (error) => directoryErrors.push(error.message));

  const directoryResponse = await directoryPage.goto(`${baseUrl}/`, {
    waitUntil: 'networkidle',
    timeout: 60_000,
  });

  if (!directoryResponse || directoryResponse.status() !== 200) {
    throw new Error('Unable to render the WordPress theme-directory screenshot surface.');
  }

  await waitForImages(directoryPage);
  await directoryPage.screenshot({
    path: path.resolve('screenshot.png'),
    fullPage: false,
  });

  if (directoryErrors.length > 0) {
    throw new Error(`Theme-directory screenshot produced page errors:\n${directoryErrors.join('\n')}`);
  }

  await directoryContext.close();
} finally {
  await browser.close();
}
