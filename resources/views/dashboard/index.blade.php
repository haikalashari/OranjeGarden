@extends('layouts.app')

@section('title')
    Dashboard | OranjeGarden
@endsection

@section('content')
<div class="space-y-6">

    <!-- Ringkasan Utama -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach ([['Total Order', $totalOrders], ['Order Aktif Saat Ini', $activeOrders], ['Total Customer', $totalCustomers], ['Total Tanaman', $totalPlants]] as [$title, $value])
        <div class="bg-orange-100 text-orange-700 p-4 rounded-xl shadow text-center">
            <h3 class="text-sm md:text-base font-semibold">{{ $title }}</h3>
            <p class="text-2xl md:text-3xl font-bold mt-1">{{ $value }}</p>
        </div>
        @endforeach
    </div>

    <!-- Grafik Order -->
    <div class="bg-white rounded-xl shadow p-4">
        <h2 class="text-lg md:text-xl font-bold text-orange-600 mb-4">Order Baru dalam 30 Hari Terakhir</h2>
        <div class="w-full overflow-x-auto">
            <canvas id="orderChart" class="w-full max-w-full h-64"></canvas>
        </div>
    </div>

    <!-- Tanaman dengan Stok Rendah -->
    <div class="bg-white rounded-xl shadow p-4">
        <h2 class="text-lg md:text-xl font-bold text-orange-600 mb-4">Tanaman dengan Stok Rendah</h2>
        <div class="overflow-x-auto">
            <table class="min-w-[600px] w-full text-sm text-left text-gray-700">
                <thead class="text-xs text-white uppercase bg-orange-500">
                    <tr>
                        <th class="px-4 py-2">Nama Tanaman</th>
                        <th class="px-4 py-2">Kategori</th>
                        <th class="px-4 py-2">Stok Tersisa</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lowStockPlants as $plant)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $plant->name }}</td>
                        <td class="px-4 py-2">{{ ucfirst($plant->category) }}</td>
                        <td class="px-4 py-2">{{ $plant->stock }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Order Aktif -->
    <div class="bg-white rounded-xl shadow p-4">
        <h2 class="text-lg md:text-xl font-bold text-orange-600 mb-4">Order Aktif Saat Ini</h2>
        <div class="overflow-x-auto">
            <table class="min-w-[700px] w-full text-sm text-left text-gray-700">
                <thead class="text-xs text-white uppercase bg-orange-500">
                    <tr>
                        <th class="px-4 py-2">Nomor</th>
                        <th class="px-4 py-2">Customer</th>
                        <th class="px-4 py-2">Tanaman</th>
                        <th class="px-4 py-2">Tanggal Mulai</th>
                        <th class="px-4 py-2">Tanggal Selesai</th>
                        <th class="px-4 py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($activeOrdersList as $index => $order)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $index + 1 }}</td>
                        <td class="px-4 py-2">{{ $order->customer->name }}</td>
                        <td class="px-4 py-2">
                            @foreach ($order->orderItems as $item)
                                {{ $item->plant->name }} (x{{ $item->quantity }}) (Batch: {{ $item->replacement_batch }})<br>
                            @endforeach
                        </td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($order->order_date)->translatedFormat('d F Y') }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($order->end_date)->translatedFormat('d F Y') }}</td>
                        <td class="px-4 py-2 text-orange-600 font-semibold">{{ $order->latestStatus->status_category->status }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('orderChart').getContext('2d');
    const orderChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($orderChartLabels),
            datasets: [{
                label: 'Jumlah Order',
                data: @json($orderChartData),
                borderColor: '#F97316',
                backgroundColor: 'rgba(249, 115, 22, 0.2)',
                borderWidth: 2,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                ticks: {
                    precision: 0 // ← Ini mencegah angka desimal
                    }
                }
            }
        }
    });
</script>
@endsection
