const { chromium } = require('playwright');

const baseUrl = process.env.BASE_URL || 'http://localhost:8000';

const users = [
  { role: 'admin', email: 'admin@islamic-edu.com' },
  { role: 'teacher', email: 'sara@islamic-edu.com' },
  { role: 'student', email: 'student@islamic-edu.com' }
];

// Try these passwords in order for each account
const passwords = ['password', 'admin123', 'teacher123', 'student123'];

(async () => {
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();

  for (const user of users) {
    console.log(`\n=== Trying login for ${user.role} (${user.email}) ===`);
    let logged = false;

    for (const pwd of passwords) {
      try {
        await page.goto(`${baseUrl}/pages/login.php`, { waitUntil: 'networkidle' });
        await page.fill('#email', user.email);
        await page.fill('#password', pwd);

        const [response] = await Promise.all([
          page.waitForResponse(resp => resp.url().endsWith('/api/auth.php') && resp.request().method() === 'POST', { timeout: 5000 }).catch(() => null),
          page.click('button[type="submit"]')
        ]);

        if (!response) {
          console.log(`No response for password '${pwd}'`);
          continue;
        }

        const data = await response.json();
        if (data && data.success) {
          console.log(`SUCCESS: logged in as ${user.role} with password '${pwd}'`);
          logged = true;
          break;
        } else {
          console.log(`failed with '${pwd}': ${data && data.message ? data.message : 'no message'}`);
        }
      } catch (err) {
        console.log(`error trying '${pwd}':`, err.message || err);
      }
    }

    if (!logged) {
      console.log(`Could not log in ${user.email} with tried passwords.`);
    }
  }

  console.log('\nDone. Leave the browser open for inspection or close it now.');
  // await browser.close();
})();
