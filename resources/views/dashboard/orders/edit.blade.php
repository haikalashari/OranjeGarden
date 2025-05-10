@extends('layouts.app')

@section('title')
    Edit Order | OranjeGarden
@endsection

@section('content')
<div class="mx-auto p-6 space-y-10">

    <!-- Header -->
    <div class="flex justify-between items-start flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-orange-600">Edit Order #{{ $order->id }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Tanggal: {{ \Carbon\Carbon::parse($order->order_date)->translatedFormat('d F Y') }} - 
                {{ \Carbon\Carbon::parse($order->end_date)->translatedFormat('d F Y') }} 
                ({{ $order->rental_duration }} Hari)
            </p>
        </div>
    </div>

    <!-- Form Edit Order -->
    <form action="{{ route('dashboard.kelola.order.edit', $order->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left Column: Item Order -->
            <div class="space-y-6 lg:col-span-2">
                <!-- Item Order -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-orange-600 mb-4">Item dalam Order</h2>
                    <div id="itemsSection" class="space-y-4">
                        @foreach ($order->orderItems as $index => $item)
                            <div class="flex items-center gap-4 item-row">
                                <select name="items[{{ $index }}][plant_id]" class="block w-full p-2 border rounded-lg">
                                    <option value="">-- Pilih Tanaman --</option>
                                    @foreach ($plants as $plant)
                                        <option value="{{ $plant->id }}" {{ $item->plant_id == $plant->id ? 'selected' : '' }}>
                                            {{ $plant->name }} -- {{ $plant->category }}
                                        </option>
                                    @endforeach
                                </select>

                                <input type="number" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" placeholder="Jumlah" min="1" class="block w-24 p-2 border rounded-lg">

                                <button type="button" class="bg-red-500 text-white px-2 rounded hover:bg-red-600" onclick="this.parentElement.remove()">
                                    &times;
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <!-- Button Tambah Item -->
                    <button type="button" onclick="addItemRow()" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 mt-3">
                        + Tambah Item
                    </button>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">Simpan Perubahan</button>
            <button type="button" onclick="window.history.back()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 ml-3">Batal</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    let itemIndex = {{ count($order->orderItems) }};

    function addItemRow() {
        const itemsSection = document.getElementById('itemsSection');

        const newItemRow = document.createElement('div');
        newItemRow.className = 'flex items-center gap-4 item-row';
        newItemRow.innerHTML = `
            <select name="items[${itemIndex}][plant_id]" class="block w-full p-2 border rounded-lg">
                <option value="">-- Pilih Tanaman --</option>
                @foreach ($plants as $plant)
                    <option value="{{ $plant->id }}">{{ $plant->name }} -- {{ $plant->category }}</option>
                @endforeach
            </select>

            <input type="number" name="items[${itemIndex}][quantity]" placeholder="Jumlah" min="1" class="block w-24 p-2 border rounded-lg">

            <button type="button" class="bg-red-500 text-white px-2 rounded hover:bg-red-600" onclick="this.parentElement.remove()">
                &times;
            </button>
        `;
        itemsSection.appendChild(newItemRow);
        itemIndex++;
    }
</script>
@endsection
