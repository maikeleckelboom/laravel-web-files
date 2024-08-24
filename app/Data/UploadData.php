<?php

namespace App\Data;

use App\UploadStatus;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class UploadData extends Data
{
    public function __construct(
        public string       $identifier,
        public string       $fileName,
        public string       $fileType,
        public int          $fileSize,
        public int          $chunkSize,
        public int          $totalChunks,
        public int          $chunkNumber,
        public UploadedFile $chunkData,
        public UploadStatus $status = UploadStatus::PENDING,
    )
    {
    }
}
