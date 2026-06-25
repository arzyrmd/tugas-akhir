<?php

namespace App\Providers;


use App\Models\Admin;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;

class FilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Filament::serving(function () {
            if ($user = Filament::auth()->user()) {
                if ($user instanceof Admin) {
                    // Cache notifications untuk mengurangi query
                    $cacheKey = "admin_notifications_{$user->id}";

                    $notifications = Cache::remember($cacheKey, 30, function () use ($user) {
                        return $user->unreadNotifications()
                            ->whereIn('type', [
                                'App\\Notifications\\NewOrderNotification',
                                'App\\Notifications\\NewCustomRequestNotification'
                            ])
                            ->limit(10) // Batasi jumlah
                            ->get();
                    });

                    foreach ($notifications as $dbNotification) {
                        $data = $dbNotification->data;

                        if ($dbNotification->type === "App\\Notifications\\NewOrderNotification") {
                            Notification::make()
                                ->title('Pesanan Baru')
                                ->icon('heroicon-o-shopping-bag')
                                ->body("Pesanan baru dari {$data['customer_name']} senilai Rp" . number_format($data['total'], 0, ',', '.'))
                                ->actions([
                                    \Filament\Notifications\Actions\Action::make('view')
                                        ->label('Lihat')
                                        ->url(route('filament.admin.resources.orders.view', ['record' => $data['order_id']])),
                                ])
                                ->persistent()
                                ->danger()
                                ->send();

                            // Mark as read dan clear cache
                            $dbNotification->markAsRead();
                            Cache::forget($cacheKey);
                        }

                      elseif ($dbNotification->type === "App\\Notifications\\NewCustomRequestNotification") {
                        Notification::make()
                            ->title('Permintaan Produk Custom Baru')
                            ->icon('heroicon-o-clipboard-document')
                            ->body("Permintaan baru dari <strong>{$data['customer_name']}</strong> dengan judul: <strong>\"{$data['title']}\"</strong>")

                            ->actions([
                                \Filament\Notifications\Actions\Action::make('view')
                                    ->label('Lihat')
                                    ->url(route('filament.admin.resources.custom-product-requests.view', ['record' => $data['request_id']])),
                            ])
                            ->persistent()
                            ->info()
                            ->send();

                        $dbNotification->markAsRead();
                        Cache::forget($cacheKey);
                    }

                    }
                }
            }
        // });
    }
}
