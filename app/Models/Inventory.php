<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Configure os atributos que serão auditados.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('estoque')
            ->logOnly(['product_id', 'qnt_estoque', 'qnt_ideal'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Atributos que podem ser preenchidos em massa.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'product_id',
        'qnt_estoque',
        'qnt_ideal',
    ];

    /**
     * Relacionamento: este registro de estoque pertence a um produto.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Movimentações de entrada/saída associadas a este estoque.
     */
    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'product_id', 'product_id');
    }
}