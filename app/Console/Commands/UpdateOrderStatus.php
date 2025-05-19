<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\StatusCategory;
use Carbon\Carbon;

class UpdateOrderStatus extends Command
{
    protected $signature = 'orders:update-status';
    protected $description = 'Update order statuses based on rental duration and current date';

    public function handle()
    {
        $orders = Order::with(['latestStatus.status_category', 'deliverer'])->get();
        $replacementInterval = 14;
    
        foreach ($orders as $order) {
            $today = Carbon::today();
            $orderDate = Carbon::parse($order->order_date);
            $endDate = Carbon::parse($order->end_date);
            $rentalDuration = $orderDate->diffInDays($endDate);
            $daySinceStart = $orderDate->diffInDays($today) + 1;
            $currentStatus = $order->latestStatus->status_category->status ?? null;
    
            $this->info("Order #{$order->id} sudah berjalan selama {$daySinceStart} hari.");
    
            if (in_array($currentStatus, ['Proses Pengantaran', 'Order Dibatalkan', 'Order Selesai'])) {
                $this->info("Order #{$order->id} diabaikan karena statusnya '{$currentStatus}'.");
                continue;
            }
    
            // Cek apakah ada pengambilan kembali yang belum selesai
            $hasPendingPickup = $order->deliverer
                ->where('status', 'Proses Pengambilan Kembali')
                ->whereNull('delivery_photo')
                ->isNotEmpty();
    
            if ($currentStatus === 'Proses Pengambilan Kembali' && $hasPendingPickup) {
                $this->info("Order #{$order->id} tetap dalam status 'Proses Pengambilan Kembali'.");
                continue;
            }
    
            $statusUpdated = false;
    
            // Jika sudah lewat masa sewa
            if ($today->gte($endDate)) {
                $this->updateOrderStatus($order, 'Proses Pengambilan Kembali');
                $statusUpdated = true;
            } else {
                // Cek apakah ada penggantian yang belum selesai
                $hasPendingReplacement = $order->deliverer
                    ->where('status', 'Proses Penggantian Tanaman')
                    ->whereNull('delivery_photo')
                    ->isNotEmpty();
    
                if ($currentStatus === 'Proses Penggantian Tanaman' && $hasPendingReplacement) {
                    $this->info("Order #{$order->id} tetap dalam status 'Proses Penggantian Tanaman'.");
                    continue;
                }
    
                // Hitung jumlah minggu penuh dan abaikan minggu terakhir
                $maxReplacements = max(0, floor($rentalDuration / $replacementInterval) - 1);
                $isExactReplacementDay = $daySinceStart > 0 && $daySinceStart % $replacementInterval === 0;
                $currentReplacementNumber = intdiv($daySinceStart, $replacementInterval);
    
                if (
                    $isExactReplacementDay &&
                    $currentReplacementNumber <= $maxReplacements &&
                    $today->lessThan($endDate)
                ) {
                    $this->updateOrderStatus($order, 'Proses Penggantian Tanaman');
                    $statusUpdated = true;
                }
    
                // Jika tidak update status penggantian dan masih dalam masa sewa
                if (!$statusUpdated && $today->between($orderDate, $endDate)) {
                    $this->updateOrderStatus($order, 'Dalam Masa Sewa');
                }
            }
        }
    
        $this->info('Order statuses updated successfully.');
    }

    private function updateOrderStatus(Order $order, string $newStatus)
    {
        $currentStatus = $order->latestStatus->status_category->status ?? null;

        if ($currentStatus !== $newStatus) {
            $statusId = StatusCategory::where('status', $newStatus)->value('id');

            if ($statusId) {
                OrderStatus::create([
                    'order_id' => $order->id,
                    'status_id' => $statusId,
                    'created_at' => Carbon::now(),
                ]);

                $this->info("Order #{$order->id} status updated to '{$newStatus}'.");
            } else {
                $this->warn("Status '{$newStatus}' tidak ditemukan di tabel status_category.");
            }
        } else {
            $this->info("Order #{$order->id} status sudah '{$newStatus}', tidak perlu update.");
        }
    }
}
