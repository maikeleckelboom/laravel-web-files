<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

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

        if ($userId) {
            $user = User::find($userId);

            if (!$user) {
                $this->error("User with ID {$userId} not found.");

                return;
            }

            $this->clearUserMediaDirectory($user);

        } else {
            $this->clearMediaDirectory();
            $this->clearTemporaryMediaDirectory();
        }

    }

    private function clearMediaDirectory(): void
    {
        $disk = config('media-library.disk_name');

        $directories = Storage::disk($disk)->directories();

        foreach ($directories as $directory) {
            Storage::disk($disk)->deleteDirectory($directory);
        }

    }

    private function clearTemporaryMediaDirectory(): void
    {
        $disk = Storage::disk(config('media-library.temporary_disk_name'));
        $directories = $disk->directories();

        foreach ($directories as $directory) {
            $disk->deleteDirectory($directory);
        }
    }

    private function clearUserMediaDirectory(User $user): void
    {
        $disk = config('media-library.disk_name');

        $directories = Storage::disk($disk)->directories("{$user->id}");

        foreach ($directories as $directory) {
            Storage::disk($disk)->deleteDirectory($directory);
        }

        $this->info("Media storage for user {$user->id} cleared.");
    }
}
