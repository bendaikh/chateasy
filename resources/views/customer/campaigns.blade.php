<x-customer-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-white">Campagnes</h2>
                <p class="text-sm text-gray-400 mt-1">Envoyez des messages personnalisés en masse</p>
            </div>
            <button class="px-6 py-3 bg-cyan-500 hover:bg-cyan-600 text-white font-semibold rounded-lg transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvelle campagne
            </button>
        </div>
    </x-slot>

    <!-- Empty State -->
    <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-16 text-center">
        <svg class="w-24 h-24 text-gray-600 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
        </svg>
        <h3 class="text-2xl font-bold text-white mb-3">Aucune campagne</h3>
        <p class="text-gray-400 mb-8 max-w-md mx-auto">
            Créez votre première campagne pour envoyer des messages personnalisés.
        </p>
        <button class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg transition inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvelle campagne
        </button>
    </div>
</x-customer-layout>
