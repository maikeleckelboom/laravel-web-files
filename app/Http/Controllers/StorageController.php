<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class StorageController extends Controller
{
    public function __invoke(Request $request, string $disk, string $id)
    {

        $media = Media::where('disk', $disk)->where('id', $id)->firstOrFail();

        logger()->info(
            $media->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        return response()->file($media->getPath());
    }
}
