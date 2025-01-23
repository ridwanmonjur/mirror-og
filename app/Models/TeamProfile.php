<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamProfile extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'team_profile';

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    protected $fillable = ['frameColor', 'backgroundColor', 'backgroundGradient', 'fontColor', 'follower_count', 'team_id'];

    public function generateStyles(): array
    {
        $backgroundStyles = $fontStyles = $frameStyles = '';

        $backgroundStyles = "background-color: #e5e7eb;"; // Default gray

        if (isset($this->backgroundBanner)) {
            $backgroundStyles = "background-image: url('/storage/{$this->backgroundBanner}');";
        } elseif (isset($this->backgroundColor)) {
            $backgroundStyles = "background-color: {$this->backgroundColor};";
        } elseif (isset($this->backgroundGradient)) {
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
