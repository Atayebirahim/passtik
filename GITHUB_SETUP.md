# GitHub Upload Guide for Passtik

## Prerequisites
- Git installed on your system
- GitHub account created
- SSH key or Personal Access Token configured

## Step-by-Step Instructions

### 1. Initialize Git Repository (if not already done)
```bash
cd /opt/lampp/htdocs/passtik
git init
```

### 2. Configure Git (First Time Only)
```bash
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"
```

### 3. Create Repository on GitHub
1. Go to https://github.com/new
2. Repository name: `passtik`
3. Description: "MikroTik Voucher Management System with WireGuard VPN"
4. Choose: Private or Public
5. **DO NOT** initialize with README, .gitignore, or license
6. Click "Create repository"

### 4. Add All Files to Git
```bash
git add .
```

### 5. Create Initial Commit
```bash
git commit -m "Initial commit: Passtik voucher management system"
```

### 6. Add GitHub Remote
Replace `YOUR_USERNAME` with your GitHub username:
```bash
git remote add origin https://github.com/YOUR_USERNAME/passtik.git
```

Or if using SSH:
```bash
git remote add origin git@github.com:YOUR_USERNAME/passtik.git
```

### 7. Push to GitHub
```bash
git branch -M main
git push -u origin main
```

## Authentication Options

### Option A: Personal Access Token (HTTPS)
1. Go to GitHub Settings → Developer settings → Personal access tokens → Tokens (classic)
2. Generate new token with `repo` scope
3. When prompted for password during push, use the token

### Option B: SSH Key (Recommended)
```bash
# Generate SSH key
ssh-keygen -t ed25519 -C "your.email@example.com"

# Copy public key
cat ~/.ssh/id_ed25519.pub

# Add to GitHub: Settings → SSH and GPG keys → New SSH key
```

## Future Updates

After initial upload, use these commands for updates:
```bash
# Check status
git status

# Add changes
git add .

# Commit changes
git commit -m "Description of changes"

# Push to GitHub
git push
```

## Important Notes

⚠️ **Security Checklist:**
- ✅ `.env` file is in `.gitignore` (sensitive data protected)
- ✅ `.env.example` created (template without secrets)
- ✅ Database credentials not exposed
- ✅ APP_KEY not exposed
- ✅ Admin credentials not in code

## Automated Deployment

Your GitHub Actions workflow (`.github/workflows/deploy.yml`) will automatically deploy to VPS when you push to the `main` branch.

Make sure to configure these GitHub Secrets:
- `VPS_HOST`: Your VPS IP address
- `VPS_USERNAME`: SSH username (usually `root`)
- `VPS_SSH_KEY`: Private SSH key for VPS access

## Quick Command Summary
```bash
cd /opt/lampp/htdocs/passtik
git init
git add .
git commit -m "Initial commit: Passtik voucher management system"
git remote add origin https://github.com/YOUR_USERNAME/passtik.git
git branch -M main
git push -u origin main
```

## Troubleshooting

**Error: "remote origin already exists"**
```bash
git remote remove origin
git remote add origin https://github.com/YOUR_USERNAME/passtik.git
```

**Error: "failed to push some refs"**
```bash
git pull origin main --rebase
git push -u origin main
```

**Large files warning**
```bash
# Check file sizes
find . -type f -size +50M

# Add to .gitignore if needed
echo "large-file.zip" >> .gitignore
```
