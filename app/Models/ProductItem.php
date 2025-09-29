<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductItem extends Model
{
    use HasFactory, LogsActivity;

    // Status
    public const STATUS_AVAILABLE = 'AVAILABLE';
    public const STATUS_LOANED    = 'LOANED';
    public const STATUS_CONSUMED  = 'CONSUMED';
    public const STATUS_BROKEN    = 'BROKEN';
    public const STATUS_LOST      = 'LOST';

    /**
     * IMPORTANTE:
     * O banco tem as colunas "internal_serial" e "external_serial" (NOT NULL).
     * Mantemos compat com o resto do código (que usa serial_internal/external)
     * via accessors/mutators abaixo.
     */
    protected $fillable = [
        'product_id',
        'internal_serial',  // <- coluna real no banco
        'external_serial',  // <- coluna real no banco
        'status',
        'current_holder_id',
        'acquired_at',
        'notes',
    ];

    protected $casts = [
        'acquired_at' => 'datetime',
    ];

    // === Logs ===
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('product_item')
            ->logOnly(['product_id','internal_serial','external_serial','status','current_holder_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // === Relações ===
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function holder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_holder_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'product_item_id');
    }

    // === Scopes úteis ===
    public function scopeAvailable($q) { return $q->where('status', self::STATUS_AVAILABLE); }
    public function scopeLoaned($q)    { return $q->where('status', self::STATUS_LOANED); }

    // === Bridge de nomes (para manter compat com views/controllers) ===
    public function getSerialInternalAttribute(): string
    {
        return (string) ($this->internal_serial ?? '');
    }
    public function setSerialInternalAttribute($value): void
    {
        $this->internal_serial = (string) $value;
    }

    public function getSerialExternalAttribute(): string
    {
        return (string) ($this->external_serial ?? '');
    }
    public function setSerialExternalAttribute($value): void
    {
        $this->external_serial = (string) $value;
    }

    // === Serial interno automático após criar
    protected static function booted()
    {
        // IMPORTANTE: como as colunas são NOT NULL, no "creating" garantimos
        // strings vazias para não falhar no insert; depois no "created"
        // geramos o serial definitivo e salvamos silenciosamente.
        static::creating(function (self $item) {
            $item->internal_serial = $item->internal_serial ?? '';
            $item->external_serial = $item->external_serial ?? '';
            $item->status = $item->status ?: self::STATUS_AVAILABLE;
        });

        static::created(function (self $item) {
            if (empty($item->internal_serial)) {
                $code = $item->product?->code ?: 'ITM';
                $item->internal_serial = sprintf('%s-%s-%06d', $code, now()->format('Y'), $item->id);
                $item->saveQuietly();
            }
        });
    }
}
