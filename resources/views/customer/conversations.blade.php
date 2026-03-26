<x-customer-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold text-white">Conversations</h2>
            <p class="text-sm text-gray-400 mt-1">Manage all your WhatsApp conversations</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Search and Filters -->
        <div class="flex gap-4">
            <div class="flex-1">
                <input 
                    type="text" 
                    placeholder="Search conversations..." 
                    class="w-full px-4 py-3 bg-[#0f1c2e] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition"
                />
            </div>
            <button class="px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-medium rounded-lg transition">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filters
            </button>
        </div>

        <!-- Conversations List -->
        <div class="bg-[#0f1c2e] border border-white/10 rounded-xl overflow-hidden">
            @if($conversations->isEmpty())
                <!-- Empty State -->
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-white mb-2">No Conversations Yet</h3>
                    <p class="text-gray-400">When customers message your WhatsApp, conversations will appear here</p>
                </div>
            @else
                <div class="divide-y divide-white/10">
                    @foreach($conversations as $conversation)
                        <a href="{{ route('app.conversation.detail', $conversation->id) }}" class="flex items-center gap-4 p-4 hover:bg-white/5 transition">
                            <!-- Avatar -->
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-semibold">{{ substr($conversation->contact_name ?? $conversation->contact_phone, 0, 1) }}</span>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="font-semibold text-white truncate">{{ $conversation->contact_name ?? $conversation->contact_phone }}</h4>
                                    <span class="text-xs text-gray-500 flex-shrink-0 ml-2">{{ $conversation->last_message_at?->diffForHumans() ?? 'N/A' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-gray-400 truncate">{{ Str::limit($conversation->last_message, 50) ?? 'No messages yet' }}</p>
                                    @if($conversation->unread_count > 0)
                                        <span class="flex-shrink-0 ml-2 px-2 py-0.5 bg-emerald-500 text-white text-xs font-semibold rounded-full">
                                            {{ $conversation->unread_count }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- WhatsApp Profile Badge -->
                            <div class="flex-shrink-0">
                                <span class="text-xs text-gray-500">
                                    {{ $conversation->whatsappProfile->name }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($conversations->hasPages())
                    <div class="p-4 border-t border-white/10">
                        {{ $conversations->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-customer-layout>
