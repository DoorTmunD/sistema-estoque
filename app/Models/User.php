<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable, LogsActivity;

    /**
     * Configurações de quais atributos serão logados.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('usuário')
            ->logOnly(['name', 'email', 'nivel'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Atributos que podem ser preenchidos em massa.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'nivel',
    ];

    /**
     * Atributos ocultos na serialização.
     *
     * @var array<int,string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts dos atributos.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    /**
     * Relacionamento: um usuário pertence a um papel.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}