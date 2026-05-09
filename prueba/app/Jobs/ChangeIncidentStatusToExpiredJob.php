<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Incident;

class ChangeIncidentStatusToExpiredJob implements ShouldQueue
{
    use Queueable;

    public $incident;

    /**
     * Initialize job instance.
     */
    public function __construct(Incident $incident)
    {
        $this->incident = $incident;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!in_array($this->incident->status, ['resolved', 'closed'])) {
            $this->incident->update(['status' => 'expired']);
        }
    }
}
