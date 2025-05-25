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
    ];

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
        $sign = $this->isPositive ? '+' : '-';
        return $sign . '$' . number_format($this->amount, 2);
    }

    /**
     * Cursor pagination scope for efficient pagination.
     */
    public function scopeCursorPaginate($query, $perPage = 15, $cursor = null, $sortField = 'date', $sortDirection = 'desc')
    {
        $allowedFields = ['date', 'amount', 'name', 'type', 'created_at', 'id'];
        $sortField = in_array($sortField, $allowedFields) ? $sortField : 'date';
        
        // Always add id as secondary sort to ensure consistency
        $query->orderBy($sortField, $sortDirection)->orderBy('id', $sortDirection);
        
        if ($cursor) {
            $operator = $sortDirection === 'desc' ? '<' : '>';
            $query->where($sortField, $operator, $cursor);
        }
        
        return $query->limit($perPage + 1); // Get one extra to check if there's more
    }

    /**
     * Get cursor paginated results with metadata.
     */
    public static function getCursorPaginated($perPage = 15, $cursor = null, $sortField = 'date', $sortDirection = 'desc')
    {
        $query = static::cursorPaginate($perPage, $cursor, $sortField, $sortDirection);
        $results = $query->get();
        
        $hasMore = $results->count() > $perPage;
        if ($hasMore) {
            $results->pop(); // Remove the extra item
        }
        
        $nextCursor = $hasMore && $results->isNotEmpty() 
            ? $results->last()->{$sortField} 
            : null;
            
        return [
            'data' => $results,
            'has_more' => $hasMore,
            'next_cursor' => $nextCursor,
            'per_page' => $perPage,
        ];
    }

}