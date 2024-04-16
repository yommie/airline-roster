<?php

namespace App\Jobs;

use App\DTOs\Roster\RosterActivityDTO;
use App\Models\User;
use App\Services\Roster\RosterImporter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RuntimeException;

class ImportRosterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param array<RosterActivityDTO> $activities
     */
    public function __construct(private string $userId, private array $activities)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(RosterImporter $rosterImporter): void
    {
        $user = User::find($this->userId);

        if (!$user) {
            throw new RuntimeException(sprintf(
                "User with Id: %s not found",
                $this->userId
            ));
        }

        $rosterImporter->import($user, $this->activities);
    }
}
