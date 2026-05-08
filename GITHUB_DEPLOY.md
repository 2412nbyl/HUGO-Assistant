HUGO-Assistant — GitHub Auto-Deploy Architecture
==================================================

## How it works

```
You (edit code)
       │
       ▼
  git push  ──►  GitHub (NN242224/HUGO-Assistant)
                      │
                      ├──► GitHub Actions (CI workflow)
                      │       checks code quality
                      │
                      └──► GitHub Webhook (POST request)
                                  │
                                  ▼
                     Laravel /webhook/github  ─►  git pull + composer + migrate
                                  │
                                  ▼
                         Laragon server UPDATED  ✅
```

## Files Created
| File | Purpose |
|------|---------|
| `.gitignore` | Ignore vendor, .env, uploads |
| `setup-git.bat` | Windows script: init git + push to GitHub |
| `deploy.bat` | Manual deploy script (git pull + update) |
| `app/Http/Controllers/WebhookController.php` | Laravel endpoint that receives GitHub webhook |
| `routes/web.php` | Added /webhook/github route |
| `.github/workflows/ci.yml` | GitHub Actions CI check |
