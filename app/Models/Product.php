<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory, LogsActivity, Searchable;

    protected $appends = [
        'total_price',
        'below_min_stock',
        'image_url',
        'available_count',
        'loaned_count',
        'consumed_count',
        // compat: muitos lugares usam $product->quantity
        'quantity',
        // ajuda na view
        // 'cost_unit', // se quiser expor também
    ];

    protected $fillable = [
        'name',
        'description',
        'unit_price',    // preço de venda / referência
        'avg_cost',      // custo médio (usado como preço de custo)
        'min_stock',
        'category_id',
        'supplier_id',
        'image_path',
        'code',          // prefixo serial interno (ex. MOUSE, TONER05)
        'is_consumable', // bloqueia empréstimo quando true
    ];

    protected $casts = [
        'unit_price'    => 'decimal:2',
        'avg_cost'      => 'decimal:2',
        'min_stock'     => 'integer',
        'is_consumable' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('produto')
            ->logOnly([
                'name','description','unit_price','avg_cost','min_stock',
                'category_id','supplier_id','image_path','code','is_consumable',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Produto \"{$this->name}\" foi {$eventName}";
    }

    // Relacionamentos
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function items(): HasMany     { return $this->hasMany(ProductItem::class); }
    public function movements(): HasMany { return $this->hasMany(InventoryMovement::class); }
    public function inventory(): HasOne  { return $this->hasOne(Inventory::class); }

    // Scout
    public function toSearchableArray()
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'description'  => $this->description,
            'quantity'     => $this->quantity,
            'min_stock'    => $this->min_stock,
            'unit_price'   => $this->unit_price,
            'category'     => optional($this->category)->name,
            'supplier'     => optional($this->supplier)->name,
            'code'         => $this->code,
            'is_consumable'=> $this->is_consumable,
        ];
    }

    public function getLabelForSearch()
    {
        return "{$this->name} (Disponíveis: {$this->available_count})"
            . ($this->category ? " | Cat: {$this->category->name}" : '')
            . ($this->supplier ? " | Forn: {$this->supplier->name}" : '');
    }

    /* ===================== Métricas / Accessors ===================== */

    // Preço de custo usado na listagem (avg_cost, fallback unit_price)
    public function getCostUnitAttribute(): float
    {
        return (float) ($this->avg_cost ?? $this->unit_price ?? 0);
    }

    // Alguns lugares esperam "quantity" -> espelha available_count
    public function getQuantityAttribute(): int
    {
        return (int) $this->available_count;
    }

    // Disponíveis (consumível usa snapshot de inventory; não-consumível conta itens AVAILABLE)
    public function getAvailableCountAttribute(): int
    {
        if ($this->is_consumable) {
            // usa o snapshot se a relação já estiver carregada; senão tenta buscar
            $inv = $this->relationLoaded('inventory') ? $this->inventory : $this->inventory()->first();
            return (int) optional($inv)->qnt_estoque ?? 0;
        }

        // se vier comCount da controller, use-o (evita N+1)
        $preCount = $this->getAttribute('available_items_count');
        if ($preCount !== null) {
            return (int) $preCount;
        }

        // fallback: consulta direta
        return (int) $this->items()
            ->where('status', ProductItem::STATUS_AVAILABLE)
            ->count();
    }

    public function getLoanedCountAttribute(): int
    {
        return (int) $this->items()->where('status', ProductItem::STATUS_LOANED)->count();
    }

    public function getConsumedCountAttribute(): int
    {
        return (int) $this->items()
            ->whereIn('status', [ProductItem::STATUS_CONSUMED, ProductItem::STATUS_BROKEN, ProductItem::STATUS_LOST])
            ->count();
    }

    public function getTotalPriceAttribute(): float
    {
        return round($this->available_count * (float) $this->cost_unit, 2);
    }

    public function getBelowMinStockAttribute(): bool
    {
        return $this->available_count < (int) $this->min_stock;
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image_path
            ? asset('storage/' . $this->image_path)
            : asset('images/placeholder.png');
    }
}
