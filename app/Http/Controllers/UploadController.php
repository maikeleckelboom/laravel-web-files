<?php

namespace App\Http\Controllers;

use App\Data\UploadData;
use App\Exceptions\ChunkCountMismatch;
use App\Exceptions\ChunkStorageFailed;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class UploadController extends Controller
{

    public function __construct(
        private readonly UploadService $uploadService

    )
    {

    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     * @throws ChunkStorageFailed
     * @throws ChunkCountMismatch
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $data = UploadData::validateAndCreate($request->all());

        $upload = $this->uploadService->store($user, $data);

        if ($upload->isCompleted()) {

            $media = $user->addMedia($upload->path)->toMediaCollection('media');

            $upload->delete();

            return response()->json([...$upload->toArray(), 'media' => $media]);
        }

        return response()->json($upload->toArray());
    }
}
