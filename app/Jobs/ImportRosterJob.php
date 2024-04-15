<?php

namespace App\Jobs;

use App\DTOs\Roster\RosterActivityDTO;
use App\Services\Roster\RosterImporter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportRosterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param array<RosterActivityDTO> $activities
     */
    public function __construct(private array $activities)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(RosterImporter $rosterImporter): void
    {
        $rosterImporter->import($this->activities);
    }
}
