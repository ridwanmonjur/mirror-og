<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VideoController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'video' => 'required|file|mimetypes:video/mp4,video/mpeg,video/quicktime|max:102400', // 100MB max
        ]);

        if ($request->hasFile('video')) {
            $file = $request->file('video');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/videos', $filename);

            $video = new Video();
            $video->title = $request->input('title');
            $video->filename = $filename;
            $video->path = $path;
            $video->save();

            return response()->json(['message' => 'Video uploaded successfully', 'video' => $video]);
        }

        return response()->json(['message' => 'No video file provided'], 400);
    }

    public function serve($id)
    {
        $video = Video::findOrFail($id);
        $path = Storage::path($video->path);

        if (!file_exists($path)) {
            abort(404);
        }

        $stream = new StreamedResponse(function() use ($path) {
            $stream = fopen($path, 'rb');
            while (!feof($stream)) {
                echo fread($stream, 8192);
                flush();
            }
            fclose($stream);
        });

        $stream->headers->set('Content-Type', 'video/mp4');
        $stream->headers->set('Content-Length', filesize($path));
        $stream->headers->set('Accept-Ranges', 'bytes');
        $stream->headers->set('Content-Disposition', 'inline; filename="' . $video->filename . '"');

        return $stream;
    }
}
