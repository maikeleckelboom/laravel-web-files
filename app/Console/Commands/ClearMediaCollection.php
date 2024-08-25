<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ClearMediaCollection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:clear {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all media collections, optionally for a specific user.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $userId = $this->argument('user_id');

        $userId
            ? $this->clearUserMediaCollection(User::find($userId))
            : $this->clearAllMediaCollections();

    }

    private function clearAllMediaCollections(): void
    {
        User::all()->each(fn($user) => $this->clearUserMediaCollection($user));
    }

    private function clearUserMediaCollection(User $user): void
    {
        $user->media()->each(fn($media) => $media->delete());
    }
}
