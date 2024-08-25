<?php

namespace App\Http\Controllers;

use App\Data\UploadData;
use App\Exceptions\ChunkCountMismatch;
use App\Models\Upload;
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
     * @throws FileIsTooBig
     * @throws FileDoesNotExist
     * @throws ChunkCountMismatch
     */
    public function store(Request $request): Upload
    {
        $user = $request->user();
        $data = UploadData::validateAndCreate($request->all());

        $upload = $this->uploadService->store($user, $data);

        if ($upload->isCompleted()) {
            $media = $user->addMedia($upload->path)->toMediaCollection('media');
            $upload = collect($upload)->merge(['media' => $media]);
        }

        return $upload;
    }

    public function destroy(Request $request, Upload $upload)
    {
        if ($request->user()->id !== $upload->user_id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $upload->delete();

        return response()->json(['message' => 'Upload deleted']);
    }
}
