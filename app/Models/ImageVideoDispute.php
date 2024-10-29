<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ImageVideoDispute extends Model
{
    use HasFactory;
    protected $fillable = [
        'file_path',
        'file_type', // 'image' or 'video'
        'mime_type',
        'size'
    ];

     public static function handleMediaUploads(Request $request, Dispute $dispute, string $type): void
    {
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $mediaFile) {
                $path = $mediaFile->store("disputes/{$type}s");
                
                $media = self::create([
                    'file_path' => $path,
                    'file_type' => str_starts_with($mediaFile->getMimeType(), 'video/') ? 'video' : 'image',
                    'mime_type' => $mediaFile->getMimeType(),
                    'size' => $mediaFile->getSize()
                ]);

                $dispute->imageVideos()->attach($media->id, ['type' => $type]);
            }
        }
    }
}
