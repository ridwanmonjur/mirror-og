<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageVideo extends Model
{
    use HasFactory;
    protected $fillable = [
        'file_path',
        'file_type', // 'image' or 'video'
        'mime_type',
        'size',
    ];

    public static function handleMediaUploads(Request $request, string $formName): array
    {
        $fileArray = [];
        $files = $request->file($formName);

        $files = is_array($files) ? $files : [$files];

        $hasFile = $request->hasFile($formName);
        
        if (!$hasFile ) return [];
        foreach ($files as $mediaFile) {
            $type = str_starts_with($mediaFile->getMimeType(), 'video/') ? 'vid' : 'img';
            $path = $mediaFile->store("media/{$type}", 'public'); // Specify disk if needed

            $media = self::create([
                'file_path' => $path,
                'file_type' => $type,
                'mime_type' => $mediaFile->getMimeType(),
                'size' => $mediaFile->getSize(),
            ]);

            $fileArray[] = $path;
        }

        return $fileArray;
    }

    public function getFileMetadata(): array
    {
        if (!Storage::disk('public')->exists($this->file_path)) {
            throw new \Exception('File not found in storage');
        }

        return [
            'path' => storage_path('app/public/' . $this->file_path),
            'size' => Storage::disk('public')->size($this->file_path),
            'mime_type' => $this->mime_type,
            'file_type' => $this->file_type,
        ];
    }

    public function deleteMedia()
    {
        if (Storage::disk('public')->exists($this->file_path)) {
            Storage::disk('public')->delete($this->file_path);
        }

        $this->delete();
    }
}
