# Fix: Product Generation Not Working on Hostinger

## Problem Identified

Your cron job only runs the **scheduler**, but queue jobs need a **queue worker** to process them.

Current cron job:
```bash
* * * * * /usr/bin/php /home/u158680994/domains/manite.site/public_html/artisan schedule:run >> /dev/null 2>&1
```

This only runs scheduled tasks. It does **NOT** process queued jobs like:
- Landing page generation
- AI image generation

## Solution: Add Queue Worker to Cron

### Step 1: Update Hostinger Cron Job

In your Hostinger cPanel → Cron Jobs, **replace** the current cron job with:

```bash
* * * * * /usr/bin/php /home/u158680994/domains/manite.site/public_html/artisan schedule:run >> /dev/null 2>&1
* * * * * /usr/bin/php /home/u158680994/domains/manite.site/public_html/artisan queue:work --stop-when-empty --max-time=50 >> /dev/null 2>&1
```

**Important:** This creates TWO cron jobs running every minute:
1. First one: Runs the scheduler
2. Second one: Processes queue jobs

### Alternative: Single Cron Job (Recommended)

If Hostinger limits number of cron jobs, use this single command:

```bash
* * * * * /usr/bin/php /home/u158680994/domains/manite.site/public_html/artisan schedule:run >> /dev/null 2>&1 && /usr/bin/php /home/u158680994/domains/manite.site/public_html/artisan queue:work --stop-when-empty --max-time=50 >> /dev/null 2>&1
```

### Step 2: Verify Queue Configuration in Production

Make sure your Hostinger `.env` file has:

```env
QUEUE_CONNECTION=database
```

Check by:
1. Login to Hostinger File Manager
2. Navigate to `/public_html/.env`
3. Look for `QUEUE_CONNECTION=database`
4. If missing, add it

### Step 3: Ensure Queue Tables Exist

The database tables need to exist. Check if these tables are in your database:
- `jobs`
- `failed_jobs`
- `job_batches`

If missing, run migrations:
```bash
ssh into your server (or use Hostinger terminal)
cd /home/u158680994/domains/manite.site/public_html
php artisan migrate
```

## Understanding the Commands

### `schedule:run`
- Runs Laravel scheduler
- Checks for scheduled tasks (hourly, daily, etc.)
- Handles cleanup tasks we added to `routes/console.php`

### `queue:work --stop-when-empty`
- Processes jobs from the queue
- `--stop-when-empty`: Exits when no more jobs (important for cron-based execution)
- `--max-time=50`: Stops after 50 seconds to avoid overlapping with next cron run

## Why This is Needed on Hostinger

**Shared hosting limitations:**
- Can't run persistent processes (`php artisan queue:work --daemon`)
- No access to process managers (Supervisor, PM2)
- Must use cron jobs to simulate a queue worker

**How it works:**
1. Every minute, cron starts the queue worker
2. Worker processes all pending jobs
3. Worker stops when queue is empty or after 50 seconds
4. Next minute, cron starts it again
5. Repeat

## Testing the Fix

### Step 1: Clear the Queue (Optional)
If you have stuck jobs, clear them first:

```bash
cd /home/u158680994/domains/manite.site/public_html
php artisan queue:clear
php artisan queue:restart
```

### Step 2: Create a Test Product

1. Go to your app → Products → Create Product
2. Fill in product details
3. Check "Generate AI Landing Page" ✅
4. Click "Create Product"

### Step 3: Monitor the Queue

Check if job was added:
```bash
php artisan queue:monitor
```

Or check the database directly:
```sql
SELECT * FROM jobs ORDER BY id DESC LIMIT 10;
```

### Step 4: Wait for Cron to Run

- Wait 1-2 minutes for the cron job to execute
- Refresh the product page
- Status should change from "pending" → "processing" → "completed"

### Step 5: Check Failed Jobs

If it still doesn't work:
```bash
php artisan queue:failed
```

Or check the database:
```sql
SELECT * FROM failed_jobs ORDER BY failed_at DESC LIMIT 10;
```

## Troubleshooting

### Issue: Jobs Stay in "Pending" Status

**Causes:**
1. Cron job not running
2. Queue worker not configured
3. Database connection issues

**Fix:**
- Verify cron job is active in Hostinger
- Check cron job execution logs
- Verify `QUEUE_CONNECTION=database` in production `.env`

### Issue: Jobs Fail Immediately

**Causes:**
1. Missing AI API key
2. Database not accessible
3. Permission issues

**Fix:**
- Check `storage/logs/laravel.log` on server
- Verify OpenAI API key is saved for your user
- Check database connectivity

### Issue: "Class not found" Errors

**Causes:**
- Composer autoload not updated on server

**Fix:**
```bash
cd /home/u158680994/domains/manite.site/public_html
composer dump-autoload
```

### Issue: Cron Job Not Running

**Verify cron is active:**
1. Hostinger cPanel → Cron Jobs
2. Check if cron is enabled
3. Check cron execution logs (if available)

**Test manually:**
```bash
/usr/bin/php /home/u158680994/domains/manite.site/public_html/artisan queue:work --stop-when-empty
```

## Expected Behavior After Fix

### Creating a Product with AI Generation:

1. **Immediately after creation:**
   - Product created ✅
   - Status: "pending"
   - Redirects to product list

2. **Within 1-2 minutes:**
   - Cron runs queue worker
   - Status changes to "processing"
   - AI generation starts

3. **After 30-60 seconds:**
   - AI generation completes
   - Status changes to "completed"
   - Landing page data populated
   - Page refreshes automatically (if polling enabled)

## Alternative: Use Railway for Queue Worker

If Hostinger cron approach doesn't work well, you can:

1. **Keep Laravel on Hostinger** (your current setup)
2. **Run queue worker on Railway** (free tier)
3. **Point Railway worker to Hostinger database**

This is more reliable but requires:
- Railway account
- Database accessible from Railway (ensure Hostinger allows external connections)
- Separate queue worker deployment

See `RAILWAY_QUEUE_SETUP_GUIDE.md` for details.

## Summary

**The Fix:**
```bash
# Add this cron job to Hostinger (every minute):
* * * * * /usr/bin/php /home/u158680994/domains/manite.site/public_html/artisan queue:work --stop-when-empty --max-time=50 >> /dev/null 2>&1
```

**Why it's needed:**
- Your current cron only runs the scheduler
- Queue jobs need a queue worker to process
- Hostinger shared hosting requires cron-based queue worker

**After applying:**
- Product generation will work
- AI landing pages will generate
- AI images will generate
- All happens in background (1-2 minute delay)

Your product generation should work after adding the queue worker cron job! 🚀
