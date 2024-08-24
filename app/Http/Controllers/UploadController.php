<?php

namespace App\Http\Controllers;

use App\Data\UploadData;
use App\Exceptions\ChunkCountMismatch;
use App\Exceptions\ChunkStorageFailed;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
//            $file = Storage::disk($upload->disk)->get($upload->file_name);
//            $user->addMedia($file)->toMediaCollection('media');

            $upload->delete();

            return response()->json($upload, 201);
        }

        return response()->json($upload);
    }
}
