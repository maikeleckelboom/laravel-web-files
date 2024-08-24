<?php

namespace App\Services;

use App\Data\UploadData;
use App\Exceptions\ChunkStorageFailed;
use App\Exceptions\ChunkCountMismatch;
use App\Models\Upload;
use App\Models\User;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class UploadService
{

    /**
     * @throws FileIsTooBig
     * @throws FileDoesNotExist
     * @throws ChunkCountMismatch
     * @throws ChunkStorageFailed
     */
    public function store(User $user, UploadData $data)
    {
        $upload = $user
            ->uploads()
            ->firstOrCreate([
                'identifier' => $data->identifier,
                'file_name' => $data->fileName,
                'file_type' => $data->fileType,
                'file_size' => $data->fileSize,
                'chunk_size' => $data->chunkSize,
                'status' => $data->status
            ])
            ->refresh();

        $this->addChunk($upload, $data->chunkData);

        if ($upload->hasReceivedAllChunks()) {
            $file = $this->assembleChunks($upload);
            $user->addMedia($file)->toMediaCollection('media');
        }

        return $upload;
    }

    /**
     * @throws ChunkStorageFailed
     */
    public function addChunk(Upload $upload, UploadedFile $uploadedFile): void
    {
        if (!$this->storeChunk($upload, $uploadedFile)) {
            throw new ChunkStorageFailed();
        }

        $upload->increment('received_chunks');
        $upload->save();
    }

    private function storeChunk(Upload $upload, UploadedFile $uploadedFile): bool
    {
        return $uploadedFile->storeAs(
            $upload->identifier,
            $upload->received_chunks,
            ['disk' => $upload->chunks_disk]
        );
    }

    /**
     * @throws ChunkCountMismatch
     */
    public function assembleChunks(Upload $upload): string
    {
        $disk = Storage::disk($upload->disk);

        $chunksDisk = Storage::disk($upload->chunks_disk);
        $chunks = $chunksDisk->files($upload->identifier);

        if (count($chunks) !== $upload->total_chunks) {
            throw new ChunkCountMismatch();
        }

        while ($chunk = array_shift($chunks)) {
            $disk->append($upload->file_name, $chunksDisk->get($chunk));
            $chunksDisk->delete($chunk);
        }

        $chunksDisk->deleteDirectory($upload->identifier);

        return $disk->path($upload->file_name);
    }
}
