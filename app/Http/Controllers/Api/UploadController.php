<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    // POST /api/upload/image — avatar ou bg_image
    public function image(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'type' => 'required|in:avatar,background',
        ]);

        $file = $request->file('file');
        $userId = $request->user()->id;
        $folder = 'uploads/' . $userId . '/' . $request->type;
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs($folder, $filename, 'public');

        return response()->json([
            'url' => Storage::disk('public')->url($path),
        ]);
    }

    // POST /api/upload/video — vidéo VSL
    public function video(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:mp4,mov,webm,avi|max:307200', // 300MB max
        ]);

        $file = $request->file('file');
        $userId = $request->user()->id;
        $folder = 'uploads/' . $userId . '/videos';
        $filename = Str::uuid() . '.' . strtolower($file->getClientOriginalExtension());

        $path = $file->storeAs($folder, $filename, 'public');

        return response()->json([
            'url'      => Storage::disk('public')->url($path),
            'filename' => $filename,
            'size'     => $file->getSize(),
        ]);
    }
}
