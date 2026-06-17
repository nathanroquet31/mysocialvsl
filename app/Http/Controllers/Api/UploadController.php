<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    private string $disk;

    public function __construct()
    {
        // Automatically switches to R2 if configured, otherwise the local public disk
        $this->disk = config('filesystems.disks.r2.key') ? 'r2' : 'public';
    }

    // POST /api/upload/image — avatar ou bg_image
    public function image(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'type' => 'required|in:avatar,background',
        ]);

        $file     = $request->file('file');
        $userId   = $request->user()->id;
        $folder   = 'uploads/' . $userId . '/' . $request->type;
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs($folder, $filename, $this->disk);

        return response()->json([
            'url' => Storage::disk($this->disk)->url($path),
        ]);
    }

    // POST /api/upload/video — VSL video
    public function video(Request $request)
    {
        set_time_limit(300); // 5 min — R2 upload can be slow for large files

        $request->validate([
            'file' => 'required|file|mimes:mp4,mov,webm|max:102400', // 100MB max
        ]);

        $file     = $request->file('file');
        $userId   = $request->user()->id;
        $folder   = 'uploads/' . $userId . '/videos';
        $filename = Str::uuid() . '.' . strtolower($file->getClientOriginalExtension());

        $path = $file->storeAs($folder, $filename, $this->disk);

        return response()->json([
            'url'      => Storage::disk($this->disk)->url($path),
            'filename' => $filename,
            'size'     => $file->getSize(),
        ]);
    }
}
