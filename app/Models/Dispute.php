<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Dispute extends Model
{
    protected $fillable = [
        'report_id',
        'match_number',
        'dispute_userId',
        'dispute_teamId',
        'dispute_teamNumber',
        'dispute_reason',
        'dispute_description',
        'response_userId',
        'response_teamId',
        'response_teamNumber',
        'response_explanation',
        'resolution_winner'
    ];

    public function imageVideos(): MorphToMany
    {
        return $this->morphToMany(ImageVideoDispute::class, 'imageable', 'dispute_image_video')
                    ->withPivot('type') // 'dispute' or 'response'
                    ->withTimestamps();
    }

    public function disputeMedia(): MorphToMany
    {
        return $this->imageVideos()->wherePivot('type', 'dispute');
    }

    public function responseMedia(): MorphToMany
    {
        return $this->imageVideos()->wherePivot('type', 'response');
    }

}
