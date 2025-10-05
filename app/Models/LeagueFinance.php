<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeagueFinance extends Model
{
    use HasFactory;

    protected $fillable = [
        'league_id',
        'expense_category_id',
        'user_id',
        'title',
        'description',
        'amount',
        'type',
        'transaction_date',
        'reference_number',
        'attachment',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    /**
     * Get the league that owns the finance record.
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * Get the expense category for this finance record.
     */
    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    /**
     * Get the user who created this finance record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for income records.
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope for expense records.
     */
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    /**
     * Scope for a specific league.
     */
    public function scopeForLeague($query, $leagueId)
    {
        return $query->where('league_id', $leagueId);
    }

    /**
     * Scope for date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    /**
     * Get formatted amount with currency symbol.
     */
    public function getFormattedAmountAttribute()
    {
        return '₹' . number_format($this->amount, 2);
    }
}