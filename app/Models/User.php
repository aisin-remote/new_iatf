<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'users';
    protected $fillable = [
        'npk',
        'name',
        'password',
        'selected_departemen_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }
    public function departemens()
    {
        return $this->belongsToMany(Departemen::class, 'departemen_user', 'user_id', 'departemen_id');
    }

    // Relasi dengan dokumen
    public function dokumen()
    {
        return $this->hasMany(Dokumen::class);
    }
    public function indukdokumen()
    {
        return $this->hasMany(IndukDokumen::class);
    }
    public function selectedDepartmen()
    {
        return $this->belongsTo(Departemen::class, '
    departemen_id');
    }
}
