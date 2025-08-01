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
use Illuminate\Support\Facades\Log;

class ImageVideoController extends Controller
{
    /**
     * Upload multiple media files
     */
    public function upload(Request $request): JsonResponse
    {
        try {
            return response()->json(
                [
                    'message' => 'Files uploaded successfully',
                    'files' => ImageVideo::handleMediaUploads($request, 'media2'),
                ],
                201,
            );
        } catch (\Illuminate\Http\Exceptions\PostTooLargeException $e) {
            Log::error('PostTooLargeException in media upload', [
                'error' => $e->getMessage(),
                'content_length' => $request->header('Content-Length'),
                'max_post_size' => ini_get('post_max_size'),
                'max_upload_size' => ini_get('upload_max_filesize'),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(
                [
                    'message' => 'File too large. Please reduce file size and try again.',
                    'error' => 'The uploaded file exceeds the maximum allowed size.',
                ],
                413,
            );
        } catch (\Exception $e) {
            Log::error('Media upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(
                [
                    'message' => 'Upload failed',
                    'error' => $e->getMessage(),
                ],
                500,
            );
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
            while (! feof($stream)) {
                echo fread($stream, 8192);
                flush();
            }
            fclose($stream);
        });

        $stream->headers->set('Content-Type', $mime);
        $stream->headers->set('Content-Length', $size);
        $stream->headers->set('Accept-Ranges', 'bytes');
        $stream->headers->set('Content-Range', 'bytes 0-'.($size - 1).'/'.$size);

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
                'message' => 'File deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'Deletion failed',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }
}
