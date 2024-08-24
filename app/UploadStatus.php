<?php

namespace App;

enum UploadStatus: string
{
    case INITIATED = 'initiated';
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case FAILED = 'failed';
    case COMPLETED = 'completed';
    case PAUSED = 'paused';


    public static function toArray(): array
    {
        return array_column(UploadStatus::cases(), 'value');
    }
}
