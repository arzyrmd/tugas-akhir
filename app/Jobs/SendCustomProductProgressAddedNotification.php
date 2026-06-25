<?php

namespace App\Jobs;

use App\Models\CustomProductProgress;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CustomProductProgressAdded;

class SendCustomProductProgressAddedNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $progress;

    public function __construct(CustomProductProgress $progress)
    {
        $this->progress = $progress;
    }

    public function handle(): void
    {
        $request = $this->progress->customProductRequest()->with('user')->first();

        if ($request && $request->user) {
            Notification::send($request->user, new CustomProductProgressAdded($this->progress));
        }
    }
}
