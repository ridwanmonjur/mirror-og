<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\ImageVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageVideoController extends Controller
{
    /**
     * Upload multiple media files
     */
    public function upload(Request $request): JsonResponse
    {
        try {
            return response()->json([
                'message' => 'Files uploaded successfully',
                'files' => ImageVideo::handleMediaUploads($request, 'media2')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Upload failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Stream video or get image
     */
    public function stream(ImageVideo $media): BinaryFileResponse|StreamedResponse
    {
        extract($media->getFileMetadata());

        if ($media->file_type === 'image') {
            return Response::file($file);
        }

        $stream = new StreamedResponse(function () use ($file) {
            $stream = fopen($file, 'rb');
            while (!feof($stream)) {
                echo fread($stream, 8192);
                flush();
            }
            fclose($stream);
        });

        $stream->headers->set('Content-Type', $mime);
        $stream->headers->set('Content-Length', $size);
        $stream->headers->set('Accept-Ranges', 'bytes');
        $stream->headers->set('Content-Range', 'bytes 0-' . ($size - 1) . '/' . $size);

        return $stream;
    }

    /**
     * Delete media file
     */
    public function destroy(ImageVideo $media): JsonResponse
    {
        try {
            $this->deleteFile();

            return response()->json([
                'message' => 'File deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Deletion failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

  
}