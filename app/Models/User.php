<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'person_id',
        'kompani_id',
        'is_active',
        'verification_status',
        'verified_at',
        'verified_by',
        'verification_note',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function roli()
    {
        return $this->belongsTo(Roli::class, 'role_id');
    }

    public function personi()
    {
        return $this->belongsTo(Personi::class, 'person_id');
    }

    public function kompania()
    {
        return $this->belongsTo(Kompania::class, 'kompani_id');
    }
}
