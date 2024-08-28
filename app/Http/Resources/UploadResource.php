<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UploadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'identifier' => $this->identifier,
            'fileName' => $this->file_name,
            'mimeType' => $this->mime_type,
            'extension' => $this->extension,
            'size' => $this->size,
            'totalChunks' => $this->total_chunks,

            'receivedChunks' => $this->received_chunks,
            'receivedBytes' => $this->received_bytes,
            'progress' => $this->progress,
            'status' => $this->status,

            'media' => $this->media,
        ];
    }
}
