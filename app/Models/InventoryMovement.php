<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    use LogsActivity;

    /**
     * Configure os campos auditados pelo spatie/laravel-activitylog
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('movimentação')
            ->logOnly([
                'product_id',
                'type',
                'quantity',
                'unit_price',
                'total_price',
                'responsible_id',
                'notes',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Campos preenchíveis em massa
     */
    protected $fillable = [
        'product_id',
        'type',          // 'in' ou 'out'
        'quantity',
        'before_stock','after_stock',
        'unit_price',
        'total_price',     
        'responsible_id',
        'notes',
    ];

    /**
     * Casts para garantir tipos corretos
     */
    protected $casts = [
        'quantity'     => 'integer',
        'before_stock' => 'integer',
  'after_stock'  => 'integer',
        'unit_price'   => 'decimal:2',
        'total_price'  => 'decimal:2',
        'responsible_id' => 'integer',
    ];

    /**
     * Relacionamento: produto movimentado
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relacionamento original com User via responsible_id
     */
    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    /**
     * Alias para simplificar acesso ao usuário
     */
    public function user(): BelongsTo
    {
        return $this->responsible();
    }
}