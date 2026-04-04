# Deploy WhatsApp Service to Railway.app

## Why Railway for WhatsApp Service?

Hostinger shared hosting **doesn't support Puppeteer/Chrome** (which whatsapp-web.js requires). Railway.app is specifically designed for Node.js apps and has full Puppeteer support.

**Benefits:**
- ✅ Free tier (500 hours/month)
- ✅ Puppeteer/Chrome pre-installed
- ✅ Automatic deployments
- ✅ Better logging
- ✅ No configuration needed
- ✅ Your Laravel app stays on Hostinger

---

## Step-by-Step Deployment to Railway

### Step 1: Sign Up for Railway

1. Go to: https://railway.app
2. Click **"Start a New Project"** or **"Login"**
3. Sign up with **GitHub** (recommended) or email

### Step 2: Create New Project

1. Click **"New Project"**
2. Select **"Deploy from GitHub repo"** (if you have the code on GitHub)
   
   **OR**
   
   Select **"Empty Project"** and we'll upload manually

### Step 3A: If Using GitHub

1. Connect your GitHub account
2. Select your repository
3. Railway will auto-detect Node.js
4. Click **"Deploy"**

### Step 3B: If Uploading Manually (Easier)

1. Click **"New"** → **"Empty Service"**
2. Click on the service
3. Go to **"Settings"** tab
4. Scroll to **"Source"** section
5. Click **"Connect Repo"** → **"Deploy from GitHub repo"**

**OR use Railway CLI:**

```bash
# Install Railway CLI
npm install -g @railway/cli

# Login
railway login

# Initialize project
railway init

# Deploy
railway up
```

### Step 4: Upload Your WhatsApp Service Files

**Files to upload:**
- `server.js`
- `package.json`
- `.env`
- `.gitignore`

Railway will automatically:
- Detect Node.js
- Run `npm install`
- Start the server with `npm start`

### Step 5: Configure Environment Variables

In Railway dashboard:

1. Click your service
2. Go to **"Variables"** tab
3. Add these variables:
   - **Key**: `LARAVEL_URL`
   - **Value**: `https://manite.site`
   
   - **Key**: `PORT`
   - **Value**: Leave empty (Railway sets this automatically)

4. Click **"Add"** to save

### Step 6: Get Your Railway URL

After deployment completes:

1. Go to **"Settings"** tab
2. Scroll to **"Domains"** section
3. Click **"Generate Domain"**
4. You'll get a URL like: `https://whatsapp-service-production-xxxx.up.railway.app`
5. **Copy this URL** - you'll need it for Laravel

### Step 7: Update Laravel .env on Hostinger

In your Laravel app on Hostinger (manite.site), edit the `.env` file:

```env
WHATSAPP_SERVICE_URL=https://whatsapp-service-production-xxxx.up.railway.app
```

Replace `xxxx` with your actual Railway domain.

### Step 8: Test the Deployment

Visit your Railway URL with `/api/status`:
```
https://whatsapp-service-production-xxxx.up.railway.app/api/status
```

You should see:
```json
{
  "success": true,
  "activeSessions": 0
}
```

---

## Quick Setup Using Railway CLI (5 Minutes)

### Install Railway CLI

```bash
npm install -g @railway/cli
```

### Deploy

```bash
# Navigate to whatsapp-service folder
cd C:\Users\Espacegamers\Documents\chateasy\whatsapp-service

# Login to Railway
railway login

# Initialize project
railway init

# Add environment variable
railway variables set LARAVEL_URL=https://manite.site

# Deploy
railway up
```

Railway will:
1. ✅ Upload your files
2. ✅ Install dependencies
3. ✅ Start the server
4. ✅ Give you a public URL

---

## Architecture After Setup

```
┌─────────────────────────────────────┐
│  Laravel App (manite.site)          │
│  Hosted on: Hostinger               │
│  Frontend, Database, Business Logic │
└──────────────┬──────────────────────┘
               │
               │ Socket.IO Connection
               │
┌──────────────▼──────────────────────┐
│  WhatsApp Service                   │
│  Hosted on: Railway.app (Free)      │
│  Node.js + Puppeteer + WhatsApp     │
└─────────────────────────────────────┘
```

**Benefits of this setup:**
- Laravel stays on Hostinger (no migration needed)
- WhatsApp service runs where Puppeteer works
- Both communicate via HTTPS
- Completely free (Railway free tier)

---

## Alternative: Use Railway GitHub Integration (Recommended)

### 1. Push Code to GitHub

```bash
cd C:\Users\Espacegamers\Documents\chateasy\whatsapp-service

git init
git add .
git commit -m "WhatsApp service for ChatEasy"
git branch -M main
git remote add origin https://github.com/yourusername/chateasy-whatsapp-service.git
git push -u origin main
```

### 2. Deploy from GitHub on Railway

1. Go to Railway dashboard
2. New Project → Deploy from GitHub
3. Select your repository
4. Railway auto-deploys on every push
5. Set environment variables in Railway dashboard

---

## Cost Comparison

### Railway.app Free Tier:
- ✅ 500 hours/month (enough for 24/7 running with room to spare)
- ✅ Includes Puppeteer/Chrome
- ✅ Automatic SSL
- ✅ No credit card required

### Hostinger VPS (Alternative):
- ❌ ~$4-8/month minimum
- Requires manual Chrome installation
- More complex setup

**Railway is the clear winner for this use case.**

---

## Troubleshooting

### Issue: Railway runs out of free hours

**Solution**: Upgrade to paid plan ($5/month) or optimize to use less resources

### Issue: Build fails

**Solution**: Check build logs, ensure `package.json` has correct dependencies

### Issue: Port binding error

**Solution**: Railway sets PORT automatically, your code already handles this:
```javascript
const PORT = process.env.PORT || 3000;
```

---

## Ready to Deploy?

Choose your method:
- **Quick**: Use Railway CLI (5 minutes)
- **Best**: Push to GitHub and deploy from there (10 minutes)
- **Manual**: Upload files via Railway dashboard (10 minutes)

All methods work perfectly. Railway + Hostinger is the ideal combo for your app! 🚀
