<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamProfile extends Model
{
    use HasFactory;

    public $timestamps = false;

    public array $otherCategories;

    protected $table = 'team_profile';

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    protected $fillable = ['frameColor', 'backgroundColor', 'backgroundGradient', 'fontColor', 
        'follower_count', 'team_id', 'default_category_id',  'other_categories'];

        public function defaultCategory(): BelongsTo
        {
            return $this->belongsTo(EventCategory::class, 'default_category_id', 'id');
        }
    
        /**
         * Set other categories from array of categories IDs
         */
        public function setOtherCategories(array $categoriesIds): void
        {
            $this->other_categories = '|' . implode('|', $categoriesIds) . '|';
            $this->save();
        }
    
        /**
         * Get other categories as array of categories IDs
         */
        public function getOtherCategories(): array
        {
            if (empty($this->other_categories)) {
                return [];
            }
            
            return array_filter(explode('|', $this->other_categories), function($value) {
                return !empty($value);
            });
        }
    
        /**
         * Add a categories to other categories
         */
        public function addOtherCategory(int $categoriesId): void
        {
            $currentCategories = $this->getOtherCategories();
            
            if (!in_array($categoriesId, $currentCategories)) {
                $currentCategories[] = $categoriesId;
                $this->setOtherCategories($currentCategories);
            }
        }
    
        /**
         * Remove a categories from other categories
         */
        public function removeOtherCategory(int $categoriesId): void
        {
            $currentCategories = $this->getOtherCategories();
            $filteredCategories = array_filter($currentCategories, function($id) use ($categoriesId) {
                return $id != $categoriesId;
            });
            
            $this->setOtherCategories($filteredCategories);
        }
    
        /**
         * Check if a categories ID exists in other categories
         */
        public function hasOtherCategory(int $categoriesId): bool
        {
            return in_array($categoriesId, $this->getOtherCategories());
        }
    
        

    public function generateStyles(): array
    {
        $backgroundStyles = $fontStyles = $frameStyles = '';

        $backgroundStyles = "background-color: #fffdfb;"; // Default gray

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
