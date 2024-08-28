<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function currentUser(Request $request)
    {
        return $request->user();
    }

    public function media(Request $request)
    {
        $mediaCollection = $request->user()
            ->getMedia('media')
            ->map(fn(Media $media) => array_merge(
                $media->toArray(),
                ['srcset' => $media->getSrcset()]
            ));

        return response()->json($mediaCollection->toArray());
    }

    public function destroyMedia(Request $request, $media)
    {
        $user = $request->user();

        $user->deleteMedia($media);

        return response()->json(['message' => 'Media deleted']);
    }
}
