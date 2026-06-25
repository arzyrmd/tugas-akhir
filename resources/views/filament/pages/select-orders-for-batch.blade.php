<x-filament-panels::page>
    {{ $this->form }}

    @if ($this->selectedBatchId)
        <div class="mt-4">
            <h3 class="text-lg font-medium">
                {{ $this->orderType === 'regular' ? 'Pesanan Reguler' : 'Produk Kustom' }} Tersedia
            </h3>
            <p class="text-sm text-gray-500 mb-4">
                Pilih pesanan yang ingin ditambahkan ke batch pengiriman
            </p>
            {{ $this->table }}
        </div>
    @else
        <div class="p-6 mt-6 text-center">
            <div
                class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-50 text-primary-500 mb-4">
                <x-heroicon-o-truck class="w-8 h-8" />
            </div>
            <h3 class="text-lg font-medium">Pilih Batch Pengiriman</h3>
            <p class="mt-1 text-sm text-gray-500">
                Pilih batch pengiriman terlebih dahulu untuk melihat pesanan yang tersedia
            </p>
        </div>
    @endif
</x-filament-panels::page>
