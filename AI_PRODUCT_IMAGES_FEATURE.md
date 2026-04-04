# AI Product Images Generation Feature

## Overview
This feature allows users to automatically generate multiple realistic product images using AI (DALL-E 3) for their product landing pages. When a user provides just one product image, the system can generate 4-5 additional professional product images with different angles and compositions.

## Features

### 1. **Automatic AI Image Generation**
- Generate 5 professional product images using OpenAI's DALL-E 3
- Multiple angles and perspectives (front view, side view, lifestyle shots, close-ups, flat lay)
- Professional product photography style with studio lighting
- Images stored in `storage/app/public/products/ai-generated/`

### 2. **Real-Time Progress Tracking**
- Live progress bar in the products table
- Shows current progress: X/5 images generated
- Percentage-based progress indicator (0-100%)
- Auto-refreshing status updates every 3 seconds

### 3. **Progress States**
- **None**: No AI images generated yet
- **Pending**: Image generation queued
- **Generating**: Currently generating images (with live progress)
- **Completed**: All images successfully generated
- **Failed**: Generation failed (with retry button)

### 4. **Professional Gallery Display**
- AI-generated images seamlessly integrated with uploaded images
- Professional image gallery on product landing pages
- Click-to-zoom modal for detailed image viewing
- Responsive grid layout (2-4 columns depending on screen size)

## Database Schema

### New Fields in `products` Table
```php
- ai_generated_images (JSON)      // Array of generated image URLs
- ai_images_status (STRING)       // Status: none/pending/generating/completed/failed
- ai_images_progress (INTEGER)    // Progress percentage (0-100)
- ai_images_total (INTEGER)       // Total images to generate (default: 5)
- ai_images_generated (INTEGER)   // Number of images generated so far
```

## Usage

### For Users

#### Creating a New Product with AI Images:
1. Go to Products → Create New Product
2. Fill in product details (name, description, price)
3. Upload at least 1 product image
4. Enable the "AI Product Images" toggle
5. Save the product
6. Watch the progress bar in the products table
7. View generated images once completed

#### Generating Images for Existing Product:
1. Go to Products list
2. Find the product
3. Click "Generate" button in the AI Images column
4. Monitor real-time progress
5. View generated images once completed

#### Viewing Generated Images:
1. Click the eye icon next to the image count badge
2. A modal will display all AI-generated images
3. Click any image to view full-size in lightbox

### For Developers

#### Trigger Image Generation Programmatically:
```php
use App\Jobs\GenerateProductImagesJob;

$product = Product::find($productId);
GenerateProductImagesJob::dispatch($product, $userId, 5);
```

#### Check Progress:
```php
$product = Product::find($productId);

return [
    'status' => $product->ai_images_status,
    'progress' => $product->ai_images_progress,
    'generated' => $product->ai_images_generated,
    'total' => $product->ai_images_total,
    'images' => $product->ai_generated_images
];
```

## API Endpoints

### Generate Images
```
POST /app/products/{id}/generate-images
```
Response:
```json
{
    "success": true,
    "message": "AI image generation started! This may take a few minutes."
}
```

### Check Progress
```
GET /app/products/{id}/image-progress
```
Response:
```json
{
    "status": "generating",
    "progress": 60,
    "generated": 3,
    "total": 5,
    "images": [
        "/storage/products/ai-generated/image1.png",
        "/storage/products/ai-generated/image2.png",
        "/storage/products/ai-generated/image3.png"
    ]
}
```

## Technical Details

### Image Generation Prompts
The system generates 5 different types of product shots:

1. **Front View**: Professional product photography, centered on white background
2. **Side Angle**: Side view with studio lighting
3. **Lifestyle Shot**: Product in use, natural setting
4. **Close-up Detail**: Detailed shot with sharp focus
5. **Flat Lay**: Overhead composition, styled photography

### Job Queue
- Uses Laravel's job queue system
- Background processing with `GenerateProductImagesJob`
- Automatic retry on failure
- Progress updates stored in database

### Performance
- Each image takes ~15-30 seconds to generate
- 2-second delay between images to respect API limits
- Total time for 5 images: ~2-3 minutes
- Progress updates every 3 seconds via AJAX polling

## Requirements

### Prerequisites
1. OpenAI API key configured in AI Settings
2. DALL-E 3 access
3. Laravel Queue worker running
4. Storage directory writable

### Configuration
1. Configure OpenAI API key:
   - Go to Settings → AI Settings
   - Add your OpenAI API key
   - Test connection

2. Start queue worker:
```bash
php artisan queue:work
```

## Error Handling

### Common Issues and Solutions

**Issue**: "OpenAI API key not configured"
- **Solution**: Add OpenAI API key in AI Settings

**Issue**: Images not generating
- **Solution**: Check queue worker is running: `php artisan queue:work`

**Issue**: Generation failed
- **Solution**: Click "Retry" button or check logs in `storage/logs/laravel.log`

**Issue**: Progress not updating
- **Solution**: Refresh the page or check browser console for errors

## UI Components

### Products Table Column
- Badge showing image count when completed
- Progress bar during generation
- Generate/Retry buttons
- Eye icon to view images

### Landing Page Gallery
- Seamless integration with uploaded images
- Professional grid layout
- Hover effects with zoom icon
- Modal lightbox for full-size viewing

## Future Enhancements
- Custom image count selection (3-10 images)
- Specific angle/style selection
- Bulk generation for multiple products
- Image editing and regeneration
- AI image quality settings
- Background removal option
- Custom prompt templates

## Troubleshooting

### Check Job Status
```bash
php artisan queue:failed
```

### Clear Failed Jobs
```bash
php artisan queue:flush
```

### Restart Queue Worker
```bash
php artisan queue:restart
```

### View Logs
```bash
tail -f storage/logs/laravel.log
```

## Cost Considerations

- DALL-E 3 Standard: ~$0.040 per image (1024x1024)
- 5 images per product: ~$0.20 per product
- Monitor usage in OpenAI dashboard
- Consider rate limits and quotas

## Security

- Images stored in public storage
- API keys encrypted in database
- User-scoped image generation
- Input validation on all endpoints
- CSRF protection on all forms

## License
Part of ChatEasy platform - All rights reserved
