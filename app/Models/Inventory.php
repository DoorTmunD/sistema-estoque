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

    protected $fillable = ['product_id','qnt_estoque','qnt_ideal'];

    // Mantém o campo derivado no JSON/arrays
    protected $appends = ['qnt_estoque'];

    protected $casts = [
        'qnt_ideal' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('estoque')
            ->logOnly(['product_id', 'qnt_estoque', 'qnt_ideal'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Estoque do produto ID {$this->product_id} foi {$eventName}";
    }

    /* ====================== Relacionamentos ====================== */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'product_id', 'product_id');
    }

    /** Itens AVAILABLE do produto (útil para eager-load de contagem). */
    public function availableItems(): HasMany
    {
        return $this->hasMany(ProductItem::class, 'product_id', 'product_id')
            ->where('status', ProductItem::STATUS_AVAILABLE);
    }

    /* ====================== Accessors ====================== */
    /**
     * Quantidade em estoque:
     * - Não-consumíveis: conta itens AVAILABLE (derivado)
     * - Consumíveis: usa snapshot (coluna qnt_estoque)
     */
    public function getQntEstoqueAttribute(): int
    {
        $this->loadMissing('product');

        if ($this->product && !$this->product->is_consumable) {
            if ($this->relationLoaded('availableItems')) {
                return (int) $this->availableItems->count();
            }
            return (int) $this->availableItems()->count();
        }

        return (int) ($this->attributes['qnt_estoque'] ?? 0);
    }
}
