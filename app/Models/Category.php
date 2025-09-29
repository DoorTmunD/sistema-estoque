<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Category extends Model
{
    use HasFactory, LogsActivity, Searchable;

    protected $fillable = ['name', 'description'];

    // SCOUT/ALGOLIA: Campos indexados na busca global
    public function toSearchableArray()
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
        ];
    }

    // Label para exibir no resultado da busca global
    public function getLabelForSearch()
    {
        return $this->name . ($this->description ? " ({$this->description})" : '');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('categoria')
            ->logOnly(['name', 'description'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Categoria \"{$this->name}\" foi {$eventName}";
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}