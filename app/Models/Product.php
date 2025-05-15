<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Atributos que serão adicionados automaticamente ao array/JSON.
     *
     * @var array<int,string>
     */
    protected $appends = [
        'total_price',
        'below_min_stock',
    ];

    /**
     * Atributos que serão auditados pela ActivityLog.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('produto')
            ->logOnly([
                'name',
                'description',
                'quantity',
                'unit_price',
                'min_stock',
                'category_id',
                'supplier_id',
            ])
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
        'description',
        'quantity',
        'unit_price',
        'min_stock',
        'category_id',
        'supplier_id',
    ];

    /**
     * Casts para garantir tipos corretos.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'unit_price'       => 'decimal:2',
        'min_stock'        => 'integer',
    ];

    /**
     * Relacionamento: o produto pertence a uma categoria.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relacionamento: o produto pertence a um fornecedor.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Accessor: calcula o preço total (quantidade × preço unitário).
     *
     * @return float
     */
    public function getTotalPriceAttribute(): float
    {
        return round($this->quantity * $this->unit_price, 2);
    }

    /**
     * Accessor: indica se o estoque está abaixo do mínimo configurado.
     *
     * @return bool
     */
    public function getBelowMinStockAttribute(): bool
    {
        return $this->quantity < $this->min_stock;
    }
}