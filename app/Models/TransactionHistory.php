<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TransactionHistory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaction_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'link',
        'amount',
        'summary',
        'isPositive',
        'date',
        'user_id'
    ];

    protected $appends = [
        'formatted_date',
        'formatted_time',
        'formatted_amount',
    ];


     /**
     * Get the formatted date in [21 May 2025] format.
     */
    public function getFormattedDateAttribute()
    {
        return $this->date->format('j M Y');
    }

    /**
     * Get the formatted time in [8:05 PM] format.
     */
    public function getFormattedTimeAttribute()
    {
        return $this->date->format('g:i A');
    }

   

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'isPositive' => 'boolean',
        'date' => 'datetime',
    ];

    /**

     * Get the formatted amount with sign.
     */
    public function getFormattedAmountAttribute()
    {
        return 'RM ' . number_format($this->amount, 2);
    }

    /**
 * Cursor pagination scope for efficient pagination.
 */
/**
 * Get cursor paginated results with metadata.
 */
public function scopeCursorPaginated($query, $perPage = 15, $cursor = null): array
{

    $query->orderBy('id', 'desc')->limit($perPage + 1);
    
    if ($cursor) {
        $operator = '<' ;
        $query->where('id', $operator, $cursor);
    }
    
    $results = $query->get();

    
    $hasMore = $results->count() > $perPage;
    if ($hasMore) {
        $results->pop();
    }
    
    $nextCursor = $hasMore && $results->isNotEmpty() 
        ? $results->last()->{'id'} 
        : null;
        
    return [
        'data' => $results,
        'has_more' => $hasMore,
        'next_cursor' => $nextCursor,
        'per_page' => $perPage,
    ];
}

    public $timestamps = NULL;

}