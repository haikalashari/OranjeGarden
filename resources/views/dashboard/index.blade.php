@extends('layouts.app')

@section('title')
    Dashboard | OranjeGarden
@endsection

@section('content')
<div class="space-y-6">

    <!-- Layout: grid for lg+, stacked for mobile -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        
        <!-- LEFT SIDE: Top Cards -->
        <div class="space-y-4 lg:col-span-1">
            @php
                $cards = [
                    ['label' => 'Total Order', 'value' => $totalOrders, 'icon' => 'shopping-cart'],
                    ['label' => 'Order Aktif Saat Ini', 'value' => $activeOrders, 'icon' => 'clock'],
                    ['label' => 'Total Customer', 'value' => $totalCustomers, 'icon' => 'users'],
                    ['label' => 'Total Tanaman', 'value' => $totalPlants, 'icon' => 'leaf'],
                    ['label' => 'Total Penghasilan', 'value' => 'Rp' . number_format($totalRevenue, 0, ',', '.'), 'icon' => 'credit-card']
                ];
            @endphp

            @foreach ($cards as $card)
            <div class="bg-orange-100 text-orange-700 p-4 rounded-xl shadow flex items-center gap-4">
                <div class="bg-orange-500 text-white rounded-full p-2">
                    @switch($card['icon'])
                        @case('shopping-cart')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 3h1.386c.51 0 .962.308 1.155.783l.964 2.316M6 9h14.25l-1.5 6H7.5l-1.5-6z" />
                                <circle cx="9" cy="19" r="1.5" />
                                <circle cx="18" cy="19" r="1.5" />
                            </svg>
                            @break
                        @case('clock')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 6v6l4 2" stroke-linecap="round" stroke-linejoin="round" />
                                <circle cx="12" cy="12" r="10" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            @break
                        @case('users')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17 20v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="9" cy="7" r="4" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M23 20v-2a4 4 0 00-3-3.87" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M16 3.13a4 4 0 010 7.75" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            @break
                        @case('leaf')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 21V12m0 0a9 9 0 00-9-9h0a9 9 0 009 9zm0 0a9 9 0 019-9h0a9 9 0 01-9 9z" />
                            </svg>
                            @break
                        @case('credit-card')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <rect x="2" y="5" width="20" height="14" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M2 10h20" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            @break
                    @endswitch
                </div>
                <div>
                    <h3 class="text-sm font-semibold">{{ $card['label'] }}</h3>
                    <p class="text-xl font-bold">{{ $card['value'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <!-- RIGHT SIDE: Tables -->
        <div class="space-y-4 lg:col-span-2">
            <!-- Tabel Stok Rendah -->
            <div class="bg-white rounded-xl shadow p-4">
                <h2 class="text-lg md:text-xl font-bold text-orange-600 mb-4">Tanaman dengan Stok Rendah</h2>
                <div class="overflow-auto max-h-[300px]">
                    <table class="min-w-full table-auto text-sm text-left text-gray-700">
                        <thead class="text-xs text-white uppercase bg-orange-500">
                            <tr>
                                <th class="px-4 py-2">Nama Tanaman</th>
                                <th class="px-4 py-2">Kategori</th>
                                <th class="px-4 py-2">Stok Tersisa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lowStockPlants as $plant)
                            <tr class="border-b hover:bg-orange-50">
                                <td class="px-4 py-2">{{ $plant->name }}</td>
                                <td class="px-4 py-2">{{ ucfirst($plant->category) }}</td>
                                <td class="px-4 py-2">{{ $plant->stock }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabel Order Aktif -->
            <div class="bg-white rounded-xl shadow p-4">
                <h2 class="text-lg md:text-xl font-bold text-orange-600 mb-4">Order Aktif Saat Ini</h2>
                <div class="overflow-auto max-h-[300px]">
                    <table class="min-w-full table-auto text-sm text-left text-gray-700">
                        <thead class="text-xs text-white uppercase bg-orange-500">
                            <tr>
                                <th class="px-4 py-2">Customer</th>
                                <th class="px-4 py-2">Tanaman</th>
                                <th class="px-4 py-2">Mulai</th>
                                <th class="px-4 py-2">Selesai</th>
                                <th class="px-4 py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activeOrdersList as $order)
                            <tr class="border-b hover:bg-orange-50 align-top">
                                <td class="px-4 py-2 whitespace-nowrap">{{ $order->customer->name }}</td>
                                <td class="px-4 py-2">
                                    @foreach ($order->orderItems as $item)
                                        <div class="mb-1">{{ $item->plant->name }} - {{ $item->plant->category}} <span class="text-xs">(x{{ $item->quantity }}) </span><span class="text-xs">(Batch: {{ $item->replacement_batch }})</span></div>
                                    @endforeach
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($order->order_date)->translatedFormat('d M Y') }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($order->end_date)->translatedFormat('d M Y') }}</td>
                                <td class="px-4 py-2 text-orange-600 font-semibold">{{ $order->latestStatus->status_category->status }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $activeOrdersList->links() }}
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Grafik -->
    <div class="bg-white rounded-xl shadow p-4">
        <h2 class="text-lg md:text-xl font-bold text-orange-600 mb-4">Order Baru dalam 30 Hari Terakhir</h2>
        <div class="w-full overflow-x-auto">
            <canvas id="orderChart" class="w-full max-w-full h-64"></canvas>
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
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });
</script>
@endsection
