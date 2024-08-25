<?php

namespace App\Http\Controllers;

use App\Data\TemporaryUploadData;
use App\Enum\UploadStatus;
use App\Exceptions\ChunkCountMismatch;
use App\Http\Resources\UploadResource;
use App\Models\TemporaryUpload;
use App\Services\UploadService;
use Illuminate\Http\JsonResponse;
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

    public function index(Request $request): JsonResponse
    {
        $uploads = $request->user()->temporaryUploads;

        return response()->json(
            UploadResource::collection($uploads)
        );
    }

    /**
     * @throws FileIsTooBig
     * @throws FileDoesNotExist
     * @throws ChunkCountMismatch
     * @return JsonResponse<UploadResource>
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = TemporaryUploadData::validateAndCreate($request->all());

        $upload = $this->uploadService->store($user, $data);

        if ($upload->isCompleted()) {

            $media = $user->addMedia($upload->path)
                ->toMediaCollection('media');

            $upload->update(['media_id' => $media->id]);
        }

        return response()->json(UploadResource::make($upload));
    }


    public function pause(Request $request, TemporaryUpload $upload)
    {
        if ($request->user()->id !== $upload->user_id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $upload->update(['status' => UploadStatus::PAUSED]);

        return response()->json(['message' => 'Upload paused']);
    }

    public function destroy(Request $request, TemporaryUpload $upload)
    {
        if ($request->user()->id !== $upload->user_id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $upload->delete();

        return response()->json(['message' => 'Upload deleted']);
    }
}
