<?php

namespace App\Jobs;

use App\Models\CustomProductRequest;
use App\Notifications\NewCustomRequestNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendNewCustomRequestNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request;

    public function __construct(CustomProductRequest $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        $adminIds = \App\Models\Admin::pluck('id');
        \App\Models\Admin::whereIn('id', $adminIds)->chunk(50, function ($admins) {
            Notification::send($admins, new NewCustomRequestNotification($this->request));
        });
    }
}
