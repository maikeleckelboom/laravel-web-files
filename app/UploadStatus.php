<?php

namespace App;

enum UploadStatus: string
{
    case QUEUED = 'queued';
    case PENDING = 'pending';
    case FAILED = 'failed';
    case COMPLETED = 'completed';
    case PAUSED = 'paused';

    public static function toArray(): array
    {
        return array_column(UploadStatus::cases(), 'value');
    }
}
