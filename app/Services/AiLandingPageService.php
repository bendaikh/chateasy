<?php

namespace App\Services;

use App\Models\AiApiSetting;
use App\Models\Product;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiLandingPageService
{
    protected $user;
    protected $aiSetting;

    public function __construct($user)
    {
        $this->user = $user;
        $this->aiSetting = AiApiSetting::where('user_id', $user->id)->first();
    }

    public function generateLandingPage(Product $product, array $languages = ['fr', 'en', 'ar'])
    {
        if (!$this->aiSetting) {
            throw new \Exception('AI API settings not configured. Please configure your AI settings first.');
        }

        $categoryName = $product->category ? $product->category->name : 'General';

        $results = [];
        foreach ($languages as $language) {
            $prompt = $this->buildPrompt($product, $categoryName, $language);

            if (!empty($this->aiSetting->openai_api_key_encrypted)) {
                $results[$language] = $this->generateWithOpenAI($prompt);
            } elseif (!empty($this->aiSetting->anthropic_api_key_encrypted)) {
                $results[$language] = $this->generateWithAnthropic($prompt);
            } else {
                throw new \Exception('No AI API key configured. Please add an OpenAI or Anthropic API key.');
            }
        }

        return $results;
    }

    protected function buildPrompt(Product $product, string $categoryName, string $language = 'fr'): string
    {
        $languageInstructions = [
            'fr' => 'Generate all content in French (Français)',
            'en' => 'Generate all content in English',
            'ar' => 'Generate all content in Arabic (العربية)'
        ];

        $instruction = $languageInstructions[$language] ?? $languageInstructions['fr'];

        return "You are a professional marketing copywriter and landing page designer. {$instruction}.

Create compelling landing page content for the following product:

Product Name: {$product->name}
Category: {$categoryName}
Price: {$product->price} MAD
" . ($product->compare_at_price ? "Original Price: {$product->compare_at_price} MAD\n" : "") . "
Description: {$product->description}

Generate a professional, conversion-optimized landing page in JSON format with these fields:

{
    \"hero_title\": \"Write a catchy headline about the product (max 60 characters)\",
    \"hero_description\": \"Write 2-3 compelling sentences highlighting the main benefits\",
    \"features\": [
        {\"title\": \"Feature name\", \"description\": \"Why this feature matters\", \"icon\": \"✓\"},
        {\"title\": \"Feature name\", \"description\": \"Why this feature matters\", \"icon\": \"⚡\"},
        {\"title\": \"Feature name\", \"description\": \"Why this feature matters\", \"icon\": \"🎯\"},
        {\"title\": \"Feature name\", \"description\": \"Why this feature matters\", \"icon\": \"💎\"}
    ],
    \"steps\": [
        {\"number\": \"1\", \"title\": \"First step\", \"description\": \"Explain what customer does\"},
        {\"number\": \"2\", \"title\": \"Second step\", \"description\": \"Explain what customer does\"},
        {\"number\": \"3\", \"title\": \"Third step\", \"description\": \"Explain what customer does\"}
    ],
    \"steps_title\": \"Section heading for the steps\",
    \"testimonials\": [
        {\"name\": \"Ahmed\", \"text\": \"J'ai commandé ce produit il y a deux semaines et je suis vraiment impressionné par la qualité. Le service client était excellent et la livraison rapide. Je recommande vivement!\", \"rating\": 5},
        {\"name\": \"Fatima\", \"text\": \"Exactement ce que je recherchais! Le rapport qualité-prix est imbattable. Mes amies m'ont déjà demandé où je l'ai acheté. Très satisfaite de mon achat!\", \"rating\": 5},
        {\"name\": \"Hassan\", \"text\": \"Produit conforme à la description. L'équipe a été très professionnelle du début à la fin. Je commanderai à nouveau sans hésiter. Merci beaucoup!\", \"rating\": 5}
    ],
    \"testimonials_title\": \"Section heading for testimonials\",
    \"faqs\": [
        {\"question\": \"Write a common question\", \"answer\": \"Write a helpful detailed answer\"},
        {\"question\": \"Write a common question\", \"answer\": \"Write a helpful detailed answer\"},
        {\"question\": \"Write a common question\", \"answer\": \"Write a helpful detailed answer\"},
        {\"question\": \"Write a common question\", \"answer\": \"Write a helpful detailed answer\"}
    ],
    \"faqs_title\": \"Section heading for FAQs\",
    \"cta\": \"Action button text\",
    \"full_description\": \"Write 3-4 detailed persuasive paragraphs about the product\",
    \"form_title\": \"Contact form heading\",
    \"form_subtitle\": \"Contact form subheading\",
    \"form_name_placeholder\": \"Name input placeholder\",
    \"form_phone_placeholder\": \"Phone input placeholder\",
    \"form_note_placeholder\": \"Note input placeholder\",
    \"form_submit_button\": \"Submit button text\"
}

CRITICAL INSTRUCTIONS FOR TESTIMONIALS:
- The testimonials array shows examples of REAL customer reviews in French
- You MUST write similar AUTHENTIC, DETAILED testimonials for this specific product
- Each testimonial should be 2-4 sentences describing actual product experience
- Use Moroccan names: Ahmed, Fatima, Hassan, Salma, Youssef, Aisha, Karim, Nadia, Omar, Zineb
- Mention specific aspects of the product, delivery, quality, or customer service
- Write as if you are real customers sharing their genuine experience
- DO NOT use generic phrases like \"testimonial text\", \"great product\", or placeholders
- Each testimonial must be unique and believable

Other requirements:
- Make content specific to {$categoryName} category and this exact product
- Focus on real benefits, not just features
- Use persuasive, action-oriented language
- All FAQs should address real concerns about buying/ordering
- Return ONLY valid JSON, no markdown or extra text
- All content must be in {$instruction}";
    }

    protected function generateWithOpenAI(string $prompt): array
    {
        try {
            $apiKey = Crypt::decryptString($this->aiSetting->openai_api_key_encrypted);
        } catch (\Throwable $e) {
            throw new \Exception('Failed to decrypt OpenAI API key.');
        }

        $model = $this->aiSetting->openai_model ?: 'gpt-4o-mini';

        $response = Http::withToken($apiKey)
            ->timeout(60)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a professional marketing copywriter. Always respond with valid JSON only, no markdown or additional text.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 2000,
            ]);

        if (!$response->successful()) {
            $error = $response->json('error.message') ?? $response->body();
            Log::error('OpenAI API Error', ['error' => $error, 'status' => $response->status()]);
            throw new \Exception('OpenAI API request failed: ' . $error);
        }

        $content = $response->json('choices.0.message.content');
        
        return $this->parseAiResponse($content);
    }

    protected function generateWithAnthropic(string $prompt): array
    {
        try {
            $apiKey = Crypt::decryptString($this->aiSetting->anthropic_api_key_encrypted);
        } catch (\Throwable $e) {
            throw new \Exception('Failed to decrypt Anthropic API key.');
        }

        $model = $this->aiSetting->anthropic_model ?: 'claude-3-5-sonnet-20241022';

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])
            ->timeout(60)
            ->post('https://api.anthropic.com/v1/messages', [
                'model' => $model,
                'max_tokens' => 2000,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
            ]);

        if (!$response->successful()) {
            $error = $response->json('error.message') ?? $response->body();
            Log::error('Anthropic API Error', ['error' => $error, 'status' => $response->status()]);
            throw new \Exception('Anthropic API request failed: ' . $error);
        }

        $content = $response->json('content.0.text');
        
        return $this->parseAiResponse($content);
    }

    protected function parseAiResponse(string $content): array
    {
        $content = trim($content);
        
        $content = preg_replace('/^```json\s*/s', '', $content);
        $content = preg_replace('/\s*```$/s', '', $content);
        $content = trim($content);

        try {
            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from AI: ' . json_last_error_msg());
            }

            if (!isset($data['hero_title']) || !isset($data['features']) || !isset($data['full_description'])) {
                throw new \Exception('AI response missing required fields.');
            }

            return $data;
        } catch (\Throwable $e) {
            Log::error('Failed to parse AI response', ['content' => $content, 'error' => $e->getMessage()]);
            throw new \Exception('Failed to parse AI response: ' . $e->getMessage());
        }
    }

    public function saveLandingPageToProduct(Product $product, array $landingPageData): void
    {
        $updateData = [];

        // Save multi-language data
        if (isset($landingPageData['fr'])) {
            $updateData['landing_page_fr'] = $landingPageData['fr'];
        }
        if (isset($landingPageData['en'])) {
            $updateData['landing_page_en'] = $landingPageData['en'];
        }
        if (isset($landingPageData['ar'])) {
            $updateData['landing_page_ar'] = $landingPageData['ar'];
        }

        // Keep backward compatibility - save French as default
        if (isset($landingPageData['fr'])) {
            $updateData['landing_page_hero_title'] = $landingPageData['fr']['hero_title'] ?? null;
            $updateData['landing_page_hero_description'] = $landingPageData['fr']['hero_description'] ?? null;
            $updateData['landing_page_features'] = $landingPageData['fr']['features'] ?? [];
            $updateData['landing_page_cta'] = $landingPageData['fr']['cta'] ?? null;
            $updateData['landing_page_content'] = $landingPageData['fr']['full_description'] ?? null;
        }

        $product->update($updateData);
    }
}
