<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OtpRequest extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'phone',
        'otp',
        'expires_at',
        'used'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    /**
     * Vérifie si l'OTP est expiré
     */
    public function isExpired(): bool
    {
        return now()->isAfter($this->expires_at);
    }

    /**
     * Marque l'OTP comme utilisé
     */
    public function markAsUsed(): void
    {
        $this->update(['used' => true]);
    }

    /**
     * Scope pour récupérer les OTP non utilisés et non expirés
     */
    public function scopeValid($query, string $phone, string $otp)
    {
        return $query->where('phone', $phone)
                    ->where('otp', $otp)
                    ->where('used', false)
                    ->where('expires_at', '>', now());
    }

    /**
     * Scope pour nettoyer les OTP expirés
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }
}