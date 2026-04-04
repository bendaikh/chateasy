<?php

namespace App\Http\Controllers;

use App\Models\SocialMediaAdsSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SocialMediaAdsController extends Controller
{
    public function facebookAds()
    {
        $user = auth()->user();
        $settings = SocialMediaAdsSetting::firstOrCreate(['user_id' => $user->id]);
        
        return view('customer.facebook-ads', compact('settings'));
    }

    public function tiktokAds()
    {
        $user = auth()->user();
        $settings = SocialMediaAdsSetting::firstOrCreate(['user_id' => $user->id]);
        
        return view('customer.tiktok-ads', compact('settings'));
    }

    public function saveFacebookSettings(Request $request)
    {
        $validated = $request->validate([
            'facebook_access_token' => 'nullable|string|max:2048',
            'facebook_ad_account_id' => 'nullable|string|max:255',
            'facebook_page_id' => 'nullable|string|max:255',
            'facebook_business_id' => 'nullable|string|max:255',
            'clear_facebook_token' => 'nullable|boolean',
        ]);

        $user = $request->user();
        $settings = SocialMediaAdsSetting::firstOrNew(['user_id' => $user->id]);
        
        $settings->facebook_ad_account_id = $validated['facebook_ad_account_id'] ?? $settings->facebook_ad_account_id;
        $settings->facebook_page_id = $validated['facebook_page_id'] ?? $settings->facebook_page_id;
        $settings->facebook_business_id = $validated['facebook_business_id'] ?? $settings->facebook_business_id;

        if ($request->boolean('clear_facebook_token')) {
            $settings->facebook_access_token_encrypted = null;
            $settings->facebook_connected = false;
            $settings->facebook_token_expires_at = null;
        } elseif (!empty($validated['facebook_access_token'])) {
            $settings->facebook_access_token_encrypted = Crypt::encryptString(trim($validated['facebook_access_token']));
            $settings->facebook_connected = true;
            $settings->facebook_token_expires_at = Carbon::now()->addDays(60);
        }

        $settings->save();

        return redirect()
            ->route('app.facebook-ads')
            ->with('success', 'Facebook Ads settings saved successfully.');
    }

    public function testFacebookConnection(Request $request)
    {
        $settings = SocialMediaAdsSetting::where('user_id', $request->user()->id)->first();

        if (!$settings || empty($settings->facebook_access_token_encrypted)) {
            return redirect()
                ->route('app.facebook-ads')
                ->with('error', 'Please save your Facebook Access Token first.');
        }

        try {
            $accessToken = Crypt::decryptString($settings->facebook_access_token_encrypted);
        } catch (\Throwable) {
            return redirect()
                ->route('app.facebook-ads')
                ->with('error', 'Unable to decrypt the saved token. Please re-enter and save it.');
        }

        $response = Http::get('https://graph.facebook.com/v18.0/me', [
            'access_token' => $accessToken,
            'fields' => 'id,name,email'
        ]);

        if (!$response->successful()) {
            $message = data_get($response->json(), 'error.message') ?: $response->body();

            return redirect()
                ->route('app.facebook-ads')
                ->with('error', is_string($message) ? $message : 'Facebook API request failed.');
        }

        $userData = $response->json();

        return redirect()
            ->route('app.facebook-ads')
            ->with('success', 'Facebook connection successful! Connected as: ' . ($userData['name'] ?? 'Unknown'));
    }

    public function disconnectFacebook(Request $request)
    {
        $settings = SocialMediaAdsSetting::where('user_id', $request->user()->id)->first();

        if ($settings) {
            $settings->facebook_access_token_encrypted = null;
            $settings->facebook_connected = false;
            $settings->facebook_token_expires_at = null;
            $settings->facebook_ad_account_id = null;
            $settings->facebook_page_id = null;
            $settings->facebook_business_id = null;
            $settings->save();
        }

        return redirect()
            ->route('app.facebook-ads')
            ->with('success', 'Facebook Ads disconnected successfully.');
    }

    public function saveTikTokSettings(Request $request)
    {
        $validated = $request->validate([
            'tiktok_access_token' => 'nullable|string|max:2048',
            'tiktok_advertiser_id' => 'nullable|string|max:255',
            'tiktok_app_id' => 'nullable|string|max:255',
            'clear_tiktok_token' => 'nullable|boolean',
        ]);

        $user = $request->user();
        $settings = SocialMediaAdsSetting::firstOrNew(['user_id' => $user->id]);
        
        $settings->tiktok_advertiser_id = $validated['tiktok_advertiser_id'] ?? $settings->tiktok_advertiser_id;
        $settings->tiktok_app_id = $validated['tiktok_app_id'] ?? $settings->tiktok_app_id;

        if ($request->boolean('clear_tiktok_token')) {
            $settings->tiktok_access_token_encrypted = null;
            $settings->tiktok_connected = false;
            $settings->tiktok_token_expires_at = null;
        } elseif (!empty($validated['tiktok_access_token'])) {
            $settings->tiktok_access_token_encrypted = Crypt::encryptString(trim($validated['tiktok_access_token']));
            $settings->tiktok_connected = true;
            $settings->tiktok_token_expires_at = Carbon::now()->addDays(60);
        }

        $settings->save();

        return redirect()
            ->route('app.tiktok-ads')
            ->with('success', 'TikTok Ads settings saved successfully.');
    }

    public function testTikTokConnection(Request $request)
    {
        $settings = SocialMediaAdsSetting::where('user_id', $request->user()->id)->first();

        if (!$settings || empty($settings->tiktok_access_token_encrypted)) {
            return redirect()
                ->route('app.tiktok-ads')
                ->with('error', 'Please save your TikTok Access Token first.');
        }

        try {
            $accessToken = Crypt::decryptString($settings->tiktok_access_token_encrypted);
        } catch (\Throwable) {
            return redirect()
                ->route('app.tiktok-ads')
                ->with('error', 'Unable to decrypt the saved token. Please re-enter and save it.');
        }

        if (empty($settings->tiktok_advertiser_id)) {
            return redirect()
                ->route('app.tiktok-ads')
                ->with('error', 'Please provide your TikTok Advertiser ID.');
        }

        $response = Http::withHeaders([
            'Access-Token' => $accessToken,
        ])->get('https://business-api.tiktok.com/open_api/v1.3/advertiser/info/', [
            'advertiser_ids' => json_encode([$settings->tiktok_advertiser_id])
        ]);

        if (!$response->successful()) {
            $message = data_get($response->json(), 'message') ?: $response->body();

            return redirect()
                ->route('app.tiktok-ads')
                ->with('error', is_string($message) ? $message : 'TikTok API request failed.');
        }

        $responseData = $response->json();
        
        if (data_get($responseData, 'code') === 0) {
            $advertiserName = data_get($responseData, 'data.list.0.name', 'Unknown');
            
            return redirect()
                ->route('app.tiktok-ads')
                ->with('success', 'TikTok connection successful! Connected advertiser: ' . $advertiserName);
        }

        return redirect()
            ->route('app.tiktok-ads')
            ->with('error', data_get($responseData, 'message', 'TikTok connection test failed.'));
    }

    public function disconnectTikTok(Request $request)
    {
        $settings = SocialMediaAdsSetting::where('user_id', $request->user()->id)->first();

        if ($settings) {
            $settings->tiktok_access_token_encrypted = null;
            $settings->tiktok_connected = false;
            $settings->tiktok_token_expires_at = null;
            $settings->tiktok_advertiser_id = null;
            $settings->tiktok_app_id = null;
            $settings->save();
        }

        return redirect()
            ->route('app.tiktok-ads')
            ->with('success', 'TikTok Ads disconnected successfully.');
    }
}
