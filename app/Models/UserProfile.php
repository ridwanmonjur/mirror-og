<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'user_profile';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected $fillable = ['frameColor', 'backgroundColor', 'backgroundGradient', 'fontColor', 'user_id'];

    public function generateStyles(): array
    {
        $backgroundStyles = $fontStyles = $frameStyles = '';
        $backgroundStyles = 'background-color: #edebea;'; // Default gray
        if (isset($this->backgroundBanner)) {
            $backgroundStyles = "background-image: url('/storage/{$this->backgroundBanner}');";
        } elseif (isset($this->backgroundColor)) {
            $backgroundStyles = "background-color: {$this->backgroundColor};";
        } elseif (isset($this->backgroundGradient)) {
            $backgroundStyles = "background-image: {$this->backgroundGradient};";
        }

        if (isset($this->fontColor)) {
            $fontStyles = "color: {$this->fontColor}; fill: {$this->fontColor};";
        }

        if (isset($this->frameColor)) {
            $frameStyles = "border-color: {$this->frameColor};";
        }

        return [
            'backgroundStyles' => $backgroundStyles,
            'fontStyles' => $fontStyles,
            'frameStyles' => $frameStyles,
        ];
    }
}
