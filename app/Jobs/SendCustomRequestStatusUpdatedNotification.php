<?php

namespace App\Jobs;

use App\Models\CustomProductRequest;
use App\Notifications\CustomRequestStatusUpdatedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCustomRequestStatusUpdatedNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected CustomProductRequest $request;
    protected string $oldStatus;

    public function __construct(CustomProductRequest $request, string $oldStatus)
    {
        $this->request = $request;
        $this->oldStatus = $oldStatus;

    }

    public function handle()
    {
        if ($this->request->user) {
            $this->request->user->notify(new CustomRequestStatusUpdatedNotification($this->request, $this->oldStatus));
        }
    }
}
