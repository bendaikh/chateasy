<?php

namespace App\Http\Controllers;

use App\Models\WhatsappProfile;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class CustomerDashboardController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        
        $stats = [
            'conversations' => Conversation::whereHas('whatsappProfile', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count(),
            'messages' => Message::whereHas('whatsappProfile', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count(),
            'orders' => 0, // Placeholder
            'active_profiles' => WhatsappProfile::where('user_id', $user->id)
                ->where('status', 'connected')
                ->count(),
            'ai_tokens' => 0, // Placeholder
            'sales_percentage' => 0, // Placeholder
        ];
        
        $recent_conversations = Conversation::whereHas('whatsappProfile', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->with(['whatsappProfile'])
        ->latest('last_message_at')
        ->take(10)
        ->get();
        
        $whatsapp_profiles = WhatsappProfile::where('user_id', $user->id)->get();
        
        return view('customer.dashboard', compact('stats', 'recent_conversations', 'whatsapp_profiles'));
    }
    
    public function whatsapp()
    {
        $user = auth()->user();
        $profiles = WhatsappProfile::where('user_id', $user->id)->get();
        
        return view('customer.whatsapp', compact('profiles'));
    }
    
    public function conversations()
    {
        $user = auth()->user();
        
        $conversations = Conversation::whereHas('whatsappProfile', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->with(['whatsappProfile'])
        ->latest('last_message_at')
        ->paginate(20);
        
        return view('customer.conversations', compact('conversations'));
    }
    
    public function conversationDetail($id)
    {
        $user = auth()->user();
        
        $conversation = Conversation::whereHas('whatsappProfile', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->with(['messages', 'whatsappProfile'])
        ->findOrFail($id);
        
        return view('customer.conversation-detail', compact('conversation'));
    }
    
    public function aiSettings()
    {
        $user = auth()->user();
        $profiles = WhatsappProfile::where('user_id', $user->id)->get();
        
        return view('customer.ai-settings', compact('profiles'));
    }
    
    public function orders()
    {
        return view('customer.orders');
    }
    
    public function products()
    {
        $user = auth()->user();
        $products = \App\Models\Product::where('user_id', $user->id)->latest()->paginate(10);
        
        return view('customer.products', compact('products'));
    }
    
    public function productsCreate()
    {
        $categories = \App\Models\Category::where('is_active', true)->orderBy('order')->orderBy('name')->get();
        
        return view('customer.products-create', compact('categories'));
    }
    
    public function productsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'stock' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:255',
        ]);
        
        $validated['user_id'] = auth()->id();
        $validated['slug'] = \Str::slug($validated['name']) . '-' . time();
        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['stock'] = $validated['stock'] ?? 0;
        
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('products', 'public');
            }
            $validated['images'] = $imagePaths;
        }
        
        \App\Models\Product::create($validated);
        
        return redirect()->route('app.products')->with('success', 'Product created successfully!');
    }
    
    public function campaigns()
    {
        return view('customer.campaigns');
    }
    
    public function categories()
    {
        $categories = \App\Models\Category::orderBy('order')->orderBy('name')->get();
        return view('customer.categories', compact('categories'));
    }
    
    public function categoriesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        
        $validated['slug'] = \Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        
        \App\Models\Category::create($validated);
        
        return redirect()->route('app.categories')->with('success', 'Category created successfully!');
    }
    
    public function categoriesUpdate(Request $request, $id)
    {
        $category = \App\Models\Category::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        
        $validated['slug'] = \Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        
        $category->update($validated);
        
        return redirect()->route('app.categories')->with('success', 'Category updated successfully!');
    }
    
    public function categoriesDestroy($id)
    {
        $category = \App\Models\Category::findOrFail($id);
        
        // Check if category has products
        if ($category->products()->count() > 0) {
            return redirect()->route('app.categories')->with('error', 'Cannot delete category with associated products.');
        }
        
        $category->delete();
        
        return redirect()->route('app.categories')->with('success', 'Category deleted successfully!');
    }
}
