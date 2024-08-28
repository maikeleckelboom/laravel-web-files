<?php

namespace App\Models;

use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Media extends BaseMedia
{
    protected $appends = [
        'original_url',
        'thumbnail_url'
    ];

    public function getThumbnailUrlAttribute(): string
    {
        return $this->getFullUrl('thumbnail');
    }

    public function getHighestOrderNumber(): int
    {
        return (int)static::where('model_type', $this->model_type)
            ->where('model_id', $this->model_id)
            ->where('collection_name', $this->collection_name)
            ->max($this->determineOrderColumnName());
    }

}
