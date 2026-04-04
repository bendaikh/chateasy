# Quick Railway Queue Worker Setup

## ✅ Changes Pushed to GitHub

Your Railway deployment will now automatically detect the `Procfile` and can run multiple services.

## Railway Setup Steps

### Option 1: Using Procfile (Automatic - Easiest)

Railway will automatically detect the `Procfile` and offer to create multiple services:

1. **Go to your Railway project dashboard**
2. Railway should detect the Procfile and ask if you want to create:
   - `web` service (your Laravel app)
   - `worker` service (queue worker)

3. **Click "Yes" or "Create Services"**

That's it! Railway will automatically run both services.

### Option 2: Manual Setup (If Procfile doesn't auto-detect)

#### Step 1: Create Queue Worker Service

1. In Railway dashboard, click **"New Service"**
2. Select **"GitHub Repo"**
3. Choose your repository: `bendaikh/chateasy`
4. Railway will create a new service

#### Step 2: Configure Worker Service

1. Click on the new service
2. Go to **Settings** → **Service Name**: Rename to `queue-worker`
3. Go to **Settings** → **Start Command**
4. Enter:
   ```bash
   php artisan queue:work --sleep=3 --tries=3 --max-time=3600
   ```

#### Step 3: Set Environment Variables

The worker needs the same environment variables as your web app:
- Copy all env vars from your `web` service to the `worker` service
- Make sure `QUEUE_CONNECTION=database` is set

#### Step 4: Deploy

Click **"Deploy"** and Railway will start the worker.

## Verify It's Working

### Check Railway Logs

1. Go to your `queue-worker` service in Railway
2. Click on **"Deployments"** → Latest deployment
3. View logs - you should see:
   ```
   Processing: App\Jobs\GenerateProductLandingPageJob
   Processed: App\Jobs\GenerateProductLandingPageJob
   ```

### Test Queue Processing

1. Go to your application
2. Create a new product with "Generate AI Landing Page" checked
3. Watch the Railway worker logs
4. You should see the job being processed

### Monitor Queue Status

Run these commands locally (pointing to your Railway database):

```bash
# Check pending jobs
php artisan queue:monitor

# Check failed jobs
php artisan queue:failed

# Check worker status
php artisan queue:work --once  # Process one job
```

## Railway Services Configuration Summary

After setup, you should have:

### Service 1: Web (chateasy-web)
- **Start Command**: `php artisan serve --host=0.0.0.0 --port=$PORT`
- **Purpose**: Serve HTTP requests
- **Port**: Railway auto-assigns

### Service 2: Worker (chateasy-queue-worker)
- **Start Command**: `php artisan queue:work --sleep=3 --tries=3 --max-time=3600`
- **Purpose**: Process background jobs
- **No HTTP port needed**

### Service 3: Scheduler (Optional)
- **Start Command**: `while true; do php artisan schedule:run; sleep 60; done`
- **Purpose**: Run scheduled tasks (cleanup, retry failed jobs)
- **Recommended for production**

## Environment Variables Required

Both web and worker services need:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=xxx
DB_PORT=3306
DB_DATABASE=xxx
DB_USERNAME=xxx
DB_PASSWORD=xxx

# Queue
QUEUE_CONNECTION=database

# AI Services
OPENAI_API_KEY=xxx (stored in database per user)

# Application
APP_KEY=xxx
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-railway-domain.railway.app
```

## Cost Optimization

### Railway Free Tier
- **500 hours/month free**
- Web service: ~720 hours/month (always on)
- Worker service: ~720 hours/month (always on)
- **Total needed**: ~1,440 hours/month

**This exceeds the free tier!** Here are options:

### Option 1: Pay for Extra Hours
- $5-10/month for the extra ~940 hours
- Most reliable, always processing

### Option 2: Start Worker Only When Needed
- Keep web service always on
- Start worker manually when you have jobs to process
- Stop worker when queue is empty
- Requires manual intervention

### Option 3: Single Service with Process Manager
- Use PM2 or Supervisor to run both web and worker in one service
- Stays within free tier
- Slightly more complex setup

### Option 4: Upgrade to Paid Plan
- Railway Pro: $20/month
- Unlimited usage
- Better resources

## Recommended Setup (Cost-Effective)

**For Light Usage:**
- Keep web service always on
- Start worker service only when generating landing pages/images
- Use Railway dashboard to start/stop worker as needed

**For Production/Heavy Usage:**
- Keep both services always on
- Upgrade to Railway Pro plan
- Add scheduler service for automatic maintenance

## Troubleshooting

### Worker Not Processing Jobs?

1. Check worker is running in Railway dashboard
2. Check logs for errors
3. Verify `QUEUE_CONNECTION=database` is set
4. Verify database connection works
5. Check `jobs` table has records:
   ```sql
   SELECT * FROM jobs ORDER BY id DESC LIMIT 10;
   ```

### Jobs Failing?

1. Check `failed_jobs` table:
   ```sql
   SELECT * FROM failed_jobs ORDER BY failed_at DESC LIMIT 10;
   ```
2. View error details
3. Fix the issue
4. Retry: `php artisan queue:retry all`

### Out of Memory?

Increase memory in job class:
```php
public $memory = 512; // MB
public $timeout = 300; // seconds
```

## Summary

✅ **Procfile added** - Railway can auto-detect web + worker services
✅ **Queue maintenance scheduled** - Auto cleanup and retry
✅ **Documentation created** - Full production setup guide

**Next Steps:**
1. Wait for Railway deployment to complete
2. Check if Railway auto-created the worker service
3. If not, manually create worker service
4. Test by creating a product with AI generation
5. Monitor Railway logs to confirm processing

Your queue system is now production-ready! 🚀
