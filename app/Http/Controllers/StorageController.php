<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class StorageController extends Controller
{
    public function __invoke(Request $request, string $disk, string $path)
    {
        $user = User::findOrFail($request->user()->id);
        $media = $user->media()->where('disk', $disk)->where('id', $path)->firstOrFail();

        return response()->file($media->getPath());
    }
}
