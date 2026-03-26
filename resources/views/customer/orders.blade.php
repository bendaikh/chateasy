<x-customer-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-white">Commandes</h2>
                <p class="text-sm text-gray-400 mt-1">Rechercher une commande...</p>
            </div>
            <button class="px-6 py-3 bg-[#0f1c2e] border border-white/10 hover:bg-white/5 text-white rounded-lg transition flex items-center gap-2">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm">30 derniers jours</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>
    </x-slot>

    <!-- Empty State -->
    <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-16 text-center">
        <svg class="w-24 h-24 text-gray-600 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
        </svg>
        <h3 class="text-2xl font-bold text-white mb-3">Aucune commande</h3>
        <p class="text-gray-400 max-w-md mx-auto">
            Aucune commande
        </p>
    </div>
</x-customer-layout>
