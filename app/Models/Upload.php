<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Upload extends Model
{

    protected $guarded = [];

    protected $appends = [
        'received_bytes',
        'total_chunks',
        'extension',
        'progress',
    ];

    protected $casts = [
        'file_size' => 'int',
        'chunk_size' => 'int',
        'received_chunks' => 'int',
        'received_bytes' => 'int',
    ];

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
        return min($this->file_size, $this->received_chunks * $this->chunk_size);
    }

    public function getTotalChunksAttribute(): int
    {
        return ceil($this->file_size / $this->chunk_size);
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


}
