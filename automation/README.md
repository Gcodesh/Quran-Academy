# Automation: Login Test

This script uses Playwright to open a browser and attempt login for three roles: admin, teacher, and student.

Prerequisites

- Node 16+ and npm
- Playwright installed (see commands)
- A running local server serving the project root (e.g., PHP built-in server)

Quick steps

1. From project root start PHP server (example):

```bash
php -S localhost:8000 -t .
```

2. Install Playwright:

```bash
npm init -y
npm i -D playwright
npx playwright install
```

3. Run the script:

```bash
node automation/login_test.js
```

If your server uses a different base URL or port, set `BASE_URL`, e.g.:

```bash
BASE_URL=http://localhost:8080 node automation/login_test.js
```

Notes

- The script will try a small list of candidate passwords; adjust `passwords` inside the script as needed.
- The script opens a visible browser window so you can watch the flow.
