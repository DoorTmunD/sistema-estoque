<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Supplier extends Model
{
    use HasFactory, LogsActivity, Searchable;

    protected $fillable = [
        'name', 'email', 'phone', 'address',
    ];

    // SCOUT/ALGOLIA: Campos indexados na busca global
    public function toSearchableArray()
    {
        return [
            'id'      => $this->id,
            'name'    => $this->name,
            'email'   => $this->email,
            'phone'   => $this->phone,
            'address' => $this->address,
        ];
    }

    // Label para exibir no resultado da busca global
    public function getLabelForSearch()
    {
        return "{$this->name}" . 
               ($this->email ? " <{$this->email}>" : '') .
               ($this->phone ? " | {$this->phone}" : '') .
               ($this->address ? " | {$this->address}" : '');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('fornecedor')
            ->logOnly(['name', 'email', 'phone', 'address'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Fornecedor \"{$this->name}\" foi {$eventName}";
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}