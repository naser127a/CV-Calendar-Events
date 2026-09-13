<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ScheduledTestJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        logger()->info('ScheduledTestJob executed successfully.');
    }
}
