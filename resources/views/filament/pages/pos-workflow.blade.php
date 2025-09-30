<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Product Selection -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Products</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach(\App\Models\Product::where('store_id', auth()->user()->store_id)->where('is_active', true)->get() as $product)
                        <div class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50" 
                             wire:click="addProduct({{ $product->id }})">
                            <h4 class="font-medium">{{ $product->name }}</h4>
                            <p class="text-sm text-gray-600">{{ $product->category->name ?? 'No Category' }}</p>
                            <p class="text-lg font-bold text-green-600">${{ number_format($product->price, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Current Order</h3>
                
                <!-- Table Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Table</label>
                    <select wire:model="selectedTable" class="w-full border rounded-md px-3 py-2">
                        <option value="">Select Table</option>
                        @foreach(\App\Models\Table::where('store_id', auth()->user()->store_id)->get() as $table)
                            <option value="{{ $table->id }}">{{ $table->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Order Items -->
                <div class="space-y-2 mb-4">
                    @forelse($selectedProducts as $key => $item)
                        <div class="flex justify-between items-center py-2 border-b">
                            <div class="flex-1">
                                <p class="font-medium">{{ $item['name'] }}</p>
                                <p class="text-sm text-gray-600">${{ number_format($item['price'], 2) }} each</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input type="number" 
                                       value="{{ $item['quantity'] }}" 
                                       wire:change="updateQuantity('{{ $key }}', $event.target.value)"
                                       class="w-16 border rounded px-2 py-1 text-center"
                                       min="1">
                                <button wire:click="removeProduct('{{ $key }}')" 
                                        class="text-red-600 hover:text-red-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No items selected</p>
                    @endforelse
                </div>

                <!-- Order Total -->
                <div class="border-t pt-4">
                    <div class="flex justify-between items-center text-xl font-bold">
                        <span>Total:</span>
                        <span>${{ number_format($orderTotal, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>