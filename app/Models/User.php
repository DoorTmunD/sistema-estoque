<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasFactory, Notifiable, LogsActivity, Searchable;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('usuário')
            ->logOnly(['name', 'email', 'nivel'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Usuário \"{$this->name}\" foi {$eventName}";
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'nivel', // Aqui está nosso "papel"
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // Se quiser um helper para checar nível do usuário:
    public function isAdmin()
    {
        return in_array($this->nivel, ['super-admin', 'adm']);
    }

    public function isOperator()
    {
        return $this->nivel === 'operador';
    }

    public function isCommon()
    {
        return $this->nivel === 'common';
    }

    // SCOUT/ALGOLIA: Campos indexados
    public function toSearchableArray()
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email,
            'nivel' => $this->nivel,
        ];
    }

    public function getLabelForSearch()
    {
        return "{$this->name}" . ($this->email ? " <{$this->email}>" : '') .
               ($this->nivel ? " [Perfil: {$this->nivel}]" : '');
    }
}