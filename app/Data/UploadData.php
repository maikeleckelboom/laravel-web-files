<?php

namespace App\Data;

use App\UploadStatus;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;

class UploadData extends Data
{
    public function __construct(
        public string       $identifier,
        public string       $fileName,
        public string       $mimeType,
        public int          $size,
        public int          $totalChunks,
        public int          $chunkNumber,
        public int          $chunkSize,
        public UploadedFile $chunkData,
        public UploadStatus $status = UploadStatus::PENDING,
    )
    {
    }
}
