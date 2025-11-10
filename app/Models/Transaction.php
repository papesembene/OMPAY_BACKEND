<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id', 'wallet_id', 'type', 'amount', 'status',
        'marchant_id', 'recipient_phone', 'orange_tx_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function marchant()
    {
        return $this->belongsTo(Marchant::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Scope pour filtrer par type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour filtrer par statut
     */
    public function scopeOfStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pour filtrer par période
     */
    public function scopeInDateRange($query, ?string $from = null, ?string $to = null)
    {
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query;
    }

    /**
     * Scope pour trier les résultats
     */
    public function scopeSorted($query, string $sortBy = 'created_at', string $sortOrder = 'desc')
    {
        return $query->orderBy($sortBy, $sortOrder);
    }

    /**
     * Scope pour les transactions récentes
     */
    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }
}