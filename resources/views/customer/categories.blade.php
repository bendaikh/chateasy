@extends('layouts.customer')

@section('content')
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                    <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    Categories
                </h2>
                <p class="text-sm text-gray-400 mt-1">Manage your product categories</p>
            </div>
            <button onclick="openCreateModal()" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Category
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 px-4 py-3 rounded-lg flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 bg-red-500/20 border border-red-500/50 text-red-400 px-4 py-3 rounded-lg">
        <p class="font-semibold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Please fix the following errors:
        </p>
        <ul class="list-disc list-inside mt-2">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Categories Grid -->
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($categories as $category)
        <div class="bg-[#0f1c2e] border border-white/10 rounded-xl p-6 hover:border-emerald-500/50 transition">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: {{ $category->color }}20;">
                    <span class="material-icons text-2xl" style="color: {{ $category->color }}">{{ $category->icon }}</span>
                </div>
                <div class="flex gap-2">
                    <button onclick="openEditModal({{ $category->id }}, '{{ $category->name }}', '{{ $category->slug }}', '{{ $category->icon }}', '{{ $category->color }}', {{ $category->order }}, {{ $category->is_active ? 'true' : 'false' }})" class="p-2 text-blue-400 hover:bg-blue-500/20 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <form action="{{ route('app.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-red-400 hover:bg-red-500/20 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">{{ $category->name }}</h3>
            <p class="text-sm text-gray-400 mb-4">{{ $category->slug }}</p>
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-400">Order: {{ $category->order }}</span>
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $category->is_active ? 'bg-emerald-500/20 text-emerald-400' : 'bg-gray-500/20 text-gray-400' }}">
                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-[#0f1c2e] border border-white/10 rounded-xl p-12 text-center">
            <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            <h3 class="text-xl font-bold text-white mb-2">No categories yet</h3>
            <p class="text-gray-400 mb-6">Create your first category to organize your products</p>
            <button onclick="openCreateModal()" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg transition inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Category
            </button>
        </div>
        @endforelse
    </div>

    <!-- Create/Edit Modal -->
    <div id="categoryModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-[#0f1c2e] border border-white/10 rounded-xl max-w-lg w-full p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 id="modalTitle" class="text-2xl font-bold text-white">Add Category</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="categoryForm" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" id="methodField" name="_method" value="POST">

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Category Name *</label>
                    <input type="text" id="name" name="name" required class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Enter category name">
                </div>

                <div>
                    <label for="icon" class="block text-sm font-medium text-gray-300 mb-2">Icon (Material Icons)</label>
                    <input type="text" id="icon" name="icon" value="category" class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="category">
                    <p class="text-xs text-gray-500 mt-1">Visit <a href="https://fonts.google.com/icons" target="_blank" class="text-emerald-400 hover:underline">Material Icons</a></p>
                </div>

                <div>
                    <label for="color" class="block text-sm font-medium text-gray-300 mb-2">Color</label>
                    <input type="color" id="color" name="color" value="#10b981" class="w-full h-12 px-2 border border-white/10 rounded-lg bg-[#0a1628]">
                </div>

                <div>
                    <label for="order" class="block text-sm font-medium text-gray-300 mb-2">Order</label>
                    <input type="number" id="order" name="order" min="0" value="0" class="w-full px-4 py-3 bg-[#0a1628] border border-white/10 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <label for="is_active" class="block text-sm font-medium text-gray-300">Active</label>
                        <p class="text-xs text-gray-500 mt-1">Make this category visible</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="is_active" name="is_active" value="1" checked class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>

                <div class="flex justify-end gap-4 pt-4">
                    <button type="button" onclick="closeModal()" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span id="submitButtonText">Create Category</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCreateModal() {
            document.getElementById('categoryModal').classList.remove('hidden');
            document.getElementById('modalTitle').textContent = 'Add Category';
            document.getElementById('submitButtonText').textContent = 'Create Category';
            document.getElementById('categoryForm').action = '{{ route("app.categories.store") }}';
            document.getElementById('methodField').value = 'POST';
            document.getElementById('categoryForm').reset();
            document.getElementById('is_active').checked = true;
        }

        function openEditModal(id, name, slug, icon, color, order, isActive) {
            document.getElementById('categoryModal').classList.remove('hidden');
            document.getElementById('modalTitle').textContent = 'Edit Category';
            document.getElementById('submitButtonText').textContent = 'Update Category';
            document.getElementById('categoryForm').action = `/app/categories/${id}`;
            document.getElementById('methodField').value = 'PUT';
            document.getElementById('name').value = name;
            document.getElementById('icon').value = icon;
            document.getElementById('color').value = color;
            document.getElementById('order').value = order;
            document.getElementById('is_active').checked = isActive;
        }

        function closeModal() {
            document.getElementById('categoryModal').classList.add('hidden');
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Close modal on backdrop click
        document.getElementById('categoryModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
@endsection
