<?php

namespace App\Http\Controllers;

use App\Data\UploadData;
use App\Exceptions\ChunkCountMismatch;
use App\Services\UploadService;
use Illuminate\Http\Request;

class UploadController extends Controller
{

    public function __construct(
        private readonly UploadService $uploadService

    )
    {

    }

    /**
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

            $upload = collect($upload)->merge(['media' => $media])->toArray();
        }

        return $upload;
    }
}
