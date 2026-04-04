# Asynchronous Product Landing Page Generation

## Overview
The product creation flow has been updated to support asynchronous AI landing page generation. When a user creates a product with AI landing page generation enabled, the product is created immediately, and the AI generation happens in the background.

## How It Works

### 1. Product Creation Flow
- User fills out the product form and checks "AI Landing Page" option
- When clicking "Create Product", the product is saved immediately
- The landing page status is set to "pending"
- A background job is dispatched to generate the landing page
- User is redirected to the products page with a success message
- The product appears in the table with a "Pending" or "Generating..." status

### 2. Landing Page Statuses
- **none**: No landing page requested
- **pending**: Generation queued, waiting to start
- **processing**: AI is currently generating the landing page
- **completed**: Landing page generated successfully
- **failed**: Generation failed (user can retry)

### 3. Background Job
The `GenerateProductLandingPageJob` handles:
- Updating status to "processing"
- Calling the AI service to generate content
- Saving the generated content to the product
- Updating status to "completed" on success
- Updating status to "failed" on error

## Running the Queue Worker

To process background jobs, you need to run Laravel's queue worker:

### Development
```bash
php artisan queue:work
```

### Production (with supervisor)
Create a supervisor configuration file `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/project/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=your-user
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/your/project/storage/logs/worker.log
stopwaitsecs=3600
```

Then reload supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

### Alternative: Using Cron (for small loads)
Add to crontab:
```
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

And in `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('queue:work --stop-when-empty')->everyMinute();
}
```

## UI Features

### Products Table
- Shows real-time status of landing page generation
- "Pending" badge (yellow) with spinner icon
- "Generating..." badge (yellow) with animated spinner
- "AI Generated" badge (purple gradient) when completed
- "Retry" button (red) if generation failed
- Auto-refreshes every 10 seconds when there are pending jobs

### Create Product Form
- Toggle for "AI Landing Page" option
- Form submits immediately without waiting for AI
- Success message indicates generation is happening in background

## Database Changes

### New Column: `landing_page_status`
Added to `products` table:
- Type: `string`
- Default: `'none'`
- Values: `'none'`, `'pending'`, `'processing'`, `'completed'`, `'failed'`

## Error Handling
- If generation fails, the status is set to "failed"
- User can retry generation by clicking the "Retry" button
- Failed jobs are logged to `storage/logs/laravel.log`

## Testing

### Test the Flow
1. Create a product with AI landing page enabled
2. Product should be created immediately
3. Check the products table - should show "Pending" status
4. Start queue worker: `php artisan queue:work`
5. Watch the status change from "Pending" → "Generating..." → "AI Generated"
6. Page auto-refreshes to show updated status

### Manual Queue Processing
```bash
# Process one job
php artisan queue:work --once

# Process jobs for 60 seconds
php artisan queue:work --max-time=60

# Process with verbose output
php artisan queue:work -vvv
```

## Configuration

### Queue Driver
Update `.env` to use database queue (default):
```env
QUEUE_CONNECTION=database
```

Or use Redis for better performance:
```env
QUEUE_CONNECTION=redis
```

### Queue Tables
Make sure you've run the migration:
```bash
php artisan migrate
```

This creates the `jobs` and `failed_jobs` tables.

## Benefits
- Immediate user feedback (product created instantly)
- Better user experience (no waiting for AI)
- Prevents timeouts on slow AI responses
- Users can continue working while AI generates
- Failed generations can be retried without recreating the product
