<?php

namespace App\Models;

use App\Enum\UploadStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TemporaryUpload extends Model
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
        'media',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::deleted(function (TemporaryUpload $upload) {
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

    public function getMediaAttribute(): Media|null
    {
        return $this->media_id ? Media::find($this->media_id) : null;
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
