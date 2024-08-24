<?php

namespace App\Services;

use App\Data\UploadData;
use App\Models\Upload;
use App\Models\User;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    /**
     * @throws Exception
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

        return $upload;
    }

    /**
     * @throws Exception
     */
    public function addChunk(Upload $upload, UploadedFile $uploadedFile): void
    {
        if (!$this->storeChunk($upload, $uploadedFile)) {
            throw new Exception('Unable to store chunk.');
        }

        $upload->increment('received_chunks');
        $upload->save();
    }

    public function storeChunk(Upload $upload, UploadedFile $uploadedFile): bool
    {
        return $uploadedFile->storeAs(
            $upload->identifier,
            $upload->received_chunks,
            ['disk' => $upload->chunks_disk]
        );
    }

    public function assembleChunks(Upload $upload): string|false
    {
        $disk = Storage::disk($upload->disk);

        $chunksDisk = Storage::disk($upload->chunks_disk);
        $chunks = $chunksDisk->files($upload->identifier);

        if (count($chunks) !== $upload->total_chunks) {
            return false;
        }

        while ($chunk = array_shift($chunks)) {
            $disk->append($upload->file_name, $chunksDisk->get($chunk));
            $chunksDisk->delete($chunk);
        }

        $chunksDisk->deleteDirectory($upload->identifier);

        return $disk->path($upload->file_name);
    }

    public function moveChunkToDisk(Upload $upload, string $chunkPath): void
    {
    }
}
