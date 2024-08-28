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
        $mediaCollection = $this->getMediaWithSrcset($request->user());

        return response()->json($mediaCollection->toArray());
    }

    public function showMedia(Request $request, int $id)
    {
        $media = $this->getMediaById($request->user(), $id);

        if (!$media) {
            return response()->json(['message' => 'Media not found'], 404);
        }

        if (str_contains($media->mime_type, 'image')) {
            return response()->json($this->appendSrcsetToResponse($media));
        }

        return response()->json($media);

    }

    private function getMediaById($user, int $id)
    {
        return $user->getMedia('media')->where('id', $id)->first();
    }

    private function getMediaWithSrcset($user)
    {
        return $user->getMedia('media')->map(fn(Media $media) => $this->appendSrcsetToResponse($media));
    }

    private function appendSrcsetToResponse(Media $media)
    {
        return array_merge($media->toArray(), ['srcset' => $media->getSrcset()]);
    }

    public function destroyMedia(Request $request, $media)
    {
        $request->user()->deleteMedia($media);
        return response()->json(['message' => 'Media deleted']);
    }

    public function mediaSize()
    {
        return auth()->user()->getMedia('media')->sum('size');
    }
}
