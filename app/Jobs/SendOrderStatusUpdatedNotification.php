<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderStatusUpdatedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendOrderStatusUpdatedNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Order $order;
    protected string $oldStatus;

    public function __construct(Order $order, string $oldStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
    }

    public function handle(): void
    {
        if ($this->order->user_id) {
            $user = User::find($this->order->user_id);
            if ($user) {
                $user->notify(new OrderStatusUpdatedNotification($this->order, $this->oldStatus));
            }
        } elseif ($this->order->email) {
            Notification::route('mail', $this->order->email)
                ->notify(new OrderStatusUpdatedNotification($this->order, $this->oldStatus));
        }
    }
}
