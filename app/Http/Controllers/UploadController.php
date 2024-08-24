<?php

namespace App\Http\Controllers;

use App\Data\UploadData;
use App\Services\UploadService;
use Exception;
use Illuminate\Http\Request;

class UploadController extends Controller
{

    public function __construct(
        private readonly UploadService $uploadService
    )
    {

    }


    /**
     * @throws Exception
     */
    public function store(Request $request)
    {
        $data = UploadData::validateAndCreate($request->all());

        $fileUpload = $this->uploadService->store($request->user(), $data);

        if ($fileUpload->isFinished()) {

            $file = $this->uploadService->assembleChunks($fileUpload);

            $request->user()->uploads()->where('id', $fileUpload->id)->delete();

            // add to media-library
            $request->user()->addMedia($file)->toMediaCollection('media');
        }

        return response()->json($fileUpload, 202);
    }
}
