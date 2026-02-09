# Auto-Deploy Setup Guide

## 1. Generate SSH Key on VPS
```bash
ssh-keygen -t ed25519 -C "github-deploy" -f ~/.ssh/github_deploy
cat ~/.ssh/github_deploy.pub >> ~/.ssh/authorized_keys
cat ~/.ssh/github_deploy  # Copy this private key
```

## 2. Add GitHub Secrets
Go to: https://github.com/Atayebirahim/passtik/settings/secrets/actions

Add these secrets:
- **VPS_HOST**: Your VPS IP or domain (e.g., passtik.net)
- **VPS_USER**: SSH username (e.g., root or ubuntu)
- **VPS_SSH_KEY**: Private key from step 1

## 3. Configure VPS for Auto-Deploy
```bash
# Allow www-data to reload PHP-FPM without password
sudo visudo
# Add this line:
www-data ALL=(ALL) NOPASSWD: /bin/systemctl reload php8.2-fpm

# Set correct permissions
cd /var/www/passtik
sudo chown -R www-data:www-data .
sudo chmod -R 755 storage bootstrap/cache
```

## 4. Test Deployment
```bash
# Push to main branch
git add .
git commit -m "Setup auto-deploy"
git push origin main

# Check GitHub Actions tab for deployment status
```

## How It Works
- Push to `main` branch triggers deployment
- GitHub Actions connects to VPS via SSH
- Pulls latest code, runs migrations, clears cache
- Reloads PHP-FPM to apply changes
- Takes ~30 seconds per deployment

## Rollback (if needed)
```bash
ssh user@passtik.net
cd /var/www/passtik
git log --oneline  # Find commit hash
git reset --hard <commit-hash>
php artisan config:cache
sudo systemctl reload php8.2-fpm
```
