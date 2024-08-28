<?php

namespace App\Http\Controllers;

use App\Data\UploadData;
use App\Enum\UploadStatus;
use App\Exceptions\ChunkCountMismatch;
use App\Http\Resources\UploadResource;
use App\Models\Upload;
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
        $uploads = $request->user()->uploads->sortByDesc('created_at')->values();

        $uploadCollection = UploadResource::collection($uploads);

        return response()->json($uploadCollection);

    }

    /**
     * @return JsonResponse<UploadResource>
     * @throws FileDoesNotExist
     * @throws ChunkCountMismatch
     * @throws FileIsTooBig
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = UploadData::validateAndCreate($request->all());

        $upload = $this->uploadService->store($user, $data);

        if ($upload->isCompleted()) {

            $media = $user
                ->addMedia($upload->path)
                ->withResponsiveImages()
                ->toMediaCollection('media');

            $upload->update(['media_id' => $media->id]);
        }

        return response()->json(UploadResource::make($upload));
    }

    public function destroy(Request $request, int|string $idOrIdentifier)
    {
        $upload = Upload::where('identifier', $idOrIdentifier)
            ->orWhere('id', $idOrIdentifier)
            ->firstOrFail();

        if ($request->user()->id !== $upload->user_id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $upload->delete();

        return response()->json(['message' => 'Upload deleted']);
    }
}
