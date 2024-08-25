<?php

namespace App\Models;

use App\UploadStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Upload extends Model
{

    protected $guarded = [];

    protected $casts = [
        'size' => 'int',
        'chunk_size' => 'int',
    ];

    protected $appends = [
        'extension',
        'total_chunks',
        'received_bytes',
        'progress',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::deleted(function (Upload $upload) {
            $disk = Storage::disk($upload->disk);

            if ($disk->allFiles() === []) {
                $disk->deleteDirectory('');
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getProgressAttribute(): float
    {
        return $this->received_chunks / $this->total_chunks * 100;
    }

    public function getReceivedBytesAttribute(): int
    {
        return min($this->size, $this->received_chunks * $this->chunk_size);
    }

    public function getTotalChunksAttribute(): int
    {
        return ceil($this->size / $this->chunk_size);
    }

    public function getExtensionAttribute(): string
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    public function isCompleted(): bool
    {
        return $this->status === UploadStatus::COMPLETED
            || $this->received_chunks === $this->total_chunks;
    }
}
