<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    use LogsActivity, Searchable;

    public const TYPE_ENTRY       = 'ENTRY';
    public const TYPE_LOAN_OUT    = 'LOAN_OUT';
    public const TYPE_LOAN_RETURN = 'LOAN_RETURN';
    public const TYPE_CONSUMPTION = 'CONSUMPTION';
    public const TYPE_ADJUST      = 'ADJUST';

    protected $fillable = [
        'product_id',
        'product_item_id',
        'type',
        'qty',           // novo
        'quantity',      // compatibilidade com schema antigo
        'before_stock','after_stock',
        'unit_cost','total_cost',
        'collaborator_id',
        'performed_by',
        'performed_at',
        'notes',
        'properties',
    ];

    protected $casts = [
        'qty'             => 'integer',
        'quantity'        => 'integer',   // compat
        'before_stock'    => 'integer',
        'after_stock'     => 'integer',
        'unit_cost'       => 'decimal:2',
        'total_cost'      => 'decimal:2',
        'collaborator_id' => 'integer',
        'performed_by'    => 'integer',
        'performed_at'    => 'datetime',
        'properties'      => 'array',
    ];

    /* ========== Mantém qty e quantity sincronizados ========== */
    public function setQtyAttribute($value): void
    {
        $v = (int) $value;
        $this->attributes['qty']      = $v;
        $this->attributes['quantity'] = $v;
    }

    public function setQuantityAttribute($value): void
    {
        $v = (int) $value;
        $this->attributes['quantity'] = $v;
        $this->attributes['qty']      = $v;
    }

    /* ===================== Activity Log ===================== */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('movimentacao')
            ->logOnly([
                'product_id','product_item_id','type','qty','quantity','unit_cost','total_cost',
                'collaborator_id','performed_by','performed_at','notes',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Movimentação de estoque (Produto ID: {$this->product_id}) {$eventName}";
    }

    /* ===================== Relações ===================== */
    public function product(): BelongsTo      { return $this->belongsTo(Product::class); }
    public function item(): BelongsTo         { return $this->belongsTo(ProductItem::class, 'product_item_id'); }
    public function collaborator(): BelongsTo { return $this->belongsTo(User::class, 'collaborator_id'); }
    public function performer(): BelongsTo    { return $this->belongsTo(User::class, 'performed_by'); }
    public function user(): BelongsTo         { return $this->belongsTo(User::class, 'performed_by'); } // alias

    /* ===================== Helpers ===================== */
    public function getQuantityAttribute(): ?int
    {
        // preferimos 'qty'; se vier nulo, usa 'quantity'
        return $this->attributes['qty'] ?? $this->attributes['quantity'] ?? null;
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_ENTRY       => 'Entrada',
            self::TYPE_LOAN_OUT    => 'Empréstimo',
            self::TYPE_LOAN_RETURN => 'Devolução',
            self::TYPE_CONSUMPTION => 'Saída/Consumo',
            self::TYPE_ADJUST      => 'Ajuste',
            default                => ucfirst(strtolower($this->type)),
        };
    }

    public function getUiKindAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_ENTRY, self::TYPE_LOAN_RETURN         => 'in',
            self::TYPE_CONSUMPTION, self::TYPE_LOAN_OUT      => 'out',
            default                                          => 'neutral',
        };
    }

    /* ===================== Scout ===================== */
    public function toSearchableArray()
    {
        return [
            'id'           => $this->id,
            'product'      => optional($this->product)->name,
            'item_serial'  => optional($this->item)->serial_internal,
            'type'         => $this->type_label,
            'qty'          => $this->qty,
            'unit_cost'    => $this->unit_cost,
            'total_cost'   => $this->total_cost,
            'collaborator' => optional($this->collaborator)->name,
            'performed_by' => optional($this->performer)->name,
            'notes'        => $this->notes,
            'created_at'   => optional($this->created_at)->format('d/m/Y H:i'),
        ];
    }

    public function getLabelForSearch()
    {
        $name   = $this->product?->name ?? '-';
        $serial = $this->item?->serial_internal ? " | Serial: {$this->item->serial_internal}" : '';
        return "{$this->type_label} de \"{$name}\" — Qtd: {$this->qty}{$serial}";
    }
}
