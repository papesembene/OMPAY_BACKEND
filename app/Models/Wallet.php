<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;
    protected $keyType = 'string';
    public $incrementing = false;

   
    protected $fillable = ['id', 'user_id', 'balance', 'reference', 'label', 'currency', 'is_primary'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'reference';
    }
}
