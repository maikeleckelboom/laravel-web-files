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

    public static function boot(): void
    {
        parent::boot();

        static::deleted(function (Upload $upload) {
            if (Storage::disk($upload->disk)->allFiles() === []) {
                Storage::disk($upload->disk)->deleteDirectory('');
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

    public function hasReceivedAllChunks(): bool
    {
        return $this->received_chunks === $this->total_chunks;
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function setCompleted(): void
    {
        $this->query()
            ->where('id', $this->id)
            ->update(['status' => UploadStatus::COMPLETED]);
    }
}
