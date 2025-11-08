<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory , Notifiable;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name', 'phone', 'secret_code', 'is_dark_mode', 'language'
    ];

    protected $hidden = ['secret_code'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}