<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

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
            'media' => $this->media ? $this->media->toArray() : null,

            'duration' => $this->formatApproxDuration(),
            'createdAt' => Carbon::parse($this->created_at)->format('d-m-Y H:i:s'),
            'updatedAt' => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }

    private function formatApproxDuration()
    {
        $duration = Carbon::parse($this->created_at)->diffInMilliseconds($this->updated_at);

        if($duration < 1000) {
            return "Less than a second";
        }

        return Carbon::parse($this->created_at)->longAbsoluteDiffForHumans($this->updated_at);

    }
}
