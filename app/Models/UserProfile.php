<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'user_profile';

    protected $fillable = ['frameColor', 'backgroundColor', 'backgroundGradient', 'fontColor', 'user_id'];

    public function generateStyles(): array
    {
        $backgroundStyles = $fontStyles = $frameStyles = '';

        if (isset($this->backgroundBanner)) {
            $backgroundStyles = "background-image: url('/storage/{$this->backgroundBanner}');";
        }

        if (isset($this->backgroundColor)) {
            $backgroundStyles = "background-color: {$this->backgroundColor};";
        }

        if (isset($this->backgroundGradient)) {
            $backgroundStyles = "background-image: {$this->backgroundGradient};";
        }

        if (isset($this->fontColor)) {
            $fontStyles = "color: {$this->fontColor};";
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
