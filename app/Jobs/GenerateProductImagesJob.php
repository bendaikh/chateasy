<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\AiApiSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GenerateProductImagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $product;
    protected $userId;
    protected $numberOfImages;

    public function __construct(Product $product, $userId, int $numberOfImages = 5)
    {
        $this->product = $product;
        $this->userId = $userId;
        $this->numberOfImages = $numberOfImages;
    }

    public function handle(): void
    {
        try {
            $this->product->update([
                'ai_images_status' => 'generating',
                'ai_images_total' => $this->numberOfImages,
                'ai_images_generated' => 0,
                'ai_images_progress' => 0,
            ]);

            $aiSetting = AiApiSetting::where('user_id', $this->userId)->first();
            
            if (!$aiSetting || empty($aiSetting->openai_api_key_encrypted)) {
                throw new \Exception('OpenAI API key not configured');
            }

            $apiKey = Crypt::decryptString($aiSetting->openai_api_key_encrypted);
            $generatedImages = [];

            $productDescription = $this->buildProductDescription();

            for ($i = 0; $i < $this->numberOfImages; $i++) {
                try {
                    $prompt = $this->generateImagePrompt($i);
                    
                    $response = Http::withToken($apiKey)
                        ->timeout(120)
                        ->post('https://api.openai.com/v1/images/generations', [
                            'model' => 'dall-e-3',
                            'prompt' => $prompt,
                            'n' => 1,
                            'size' => '1024x1024',
                            'quality' => 'standard',
                        ]);

                    if ($response->successful()) {
                        $imageUrl = $response->json()['data'][0]['url'];
                        
                        $imageContent = Http::timeout(60)->get($imageUrl)->body();
                        $filename = 'products/ai-generated/' . uniqid() . '-' . time() . '.png';
                        Storage::disk('public')->put($filename, $imageContent);
                        
                        $generatedImages[] = Storage::url($filename);
                        
                        $progress = (int) ((($i + 1) / $this->numberOfImages) * 100);
                        
                        $this->product->update([
                            'ai_generated_images' => $generatedImages,
                            'ai_images_generated' => $i + 1,
                            'ai_images_progress' => $progress,
                        ]);

                        Log::info("Generated image " . ($i + 1) . " of {$this->numberOfImages} for product: {$this->product->id}");
                        
                        sleep(2);
                    } else {
                        Log::error("Failed to generate image " . ($i + 1) . " for product {$this->product->id}: " . $response->body());
                    }
                } catch (\Exception $e) {
                    Log::error("Error generating image " . ($i + 1) . " for product {$this->product->id}: " . $e->getMessage());
                }
            }

            if (count($generatedImages) > 0) {
                $this->product->update([
                    'ai_images_status' => 'completed',
                    'ai_images_progress' => 100,
                ]);
                Log::info("Successfully generated " . count($generatedImages) . " images for product: {$this->product->id}");
            } else {
                throw new \Exception('No images were generated successfully');
            }

        } catch (\Exception $e) {
            $this->product->update(['ai_images_status' => 'failed']);
            Log::error('Failed to generate images for product ' . $this->product->id . ': ' . $e->getMessage());
            throw $e;
        }
    }

    private function buildProductDescription(): string
    {
        return trim($this->product->name . '. ' . ($this->product->description ?? ''));
    }

    private function generateImagePrompt(int $index): string
    {
        $productName = $this->product->name;
        $description = $this->product->description ?? '';
        
        $angles = [
            "Professional product photography of {$productName}, front view, centered on white background, studio lighting, high quality, realistic, e-commerce style",
            "Professional product photography of {$productName}, side angle view, white background, studio lighting, detailed, commercial photography style",
            "Professional lifestyle shot of {$productName} in use, natural setting, professional photography, high quality, realistic lighting",
            "Professional product photography of {$productName}, close-up detail shot, white background, studio lighting, sharp focus, high resolution",
            "Professional flat lay composition featuring {$productName}, overhead view, styled product photography, white background, aesthetic arrangement"
        ];

        if ($index < count($angles)) {
            $prompt = $angles[$index];
        } else {
            $prompt = "Professional product photography of {$productName}, elegant composition, white background, studio lighting, e-commerce style";
        }

        if (!empty($description)) {
            $shortDesc = substr($description, 0, 100);
            $prompt .= ". Product details: {$shortDesc}";
        }

        return $prompt;
    }

    public function failed(\Throwable $exception): void
    {
        $this->product->update(['ai_images_status' => 'failed']);
        Log::error('Image generation job failed for product ' . $this->product->id . ': ' . $exception->getMessage());
    }
}
