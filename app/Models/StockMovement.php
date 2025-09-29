<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class StockMovement extends Model
{
    use SoftDeletes, LogsActivity;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'product_id',
        'user_id',
        'movement_type',       // 'entrada' ou 'saida'
        'quantity',
        'observation',
        'executed_at',         // Data/hora da operação
        // adicione outros campos conforme sua estrutura
    ];

    // Casts automáticos (dates, etc)
    protected $casts = [
        'executed_at' => 'datetime',
    ];

    // Logs automáticos do Spatie Activitylog
    protected static $logAttributes = [
        'product_id',
        'user_id',
        'movement_type',
        'quantity',
        'observation',
        'executed_at',
    ];
    protected static $logName = 'stock_movement';
    protected static $logOnlyDirty = true;

    /**
     * Produto relacionado a esta movimentação
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Usuário que executou a movimentação
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Arquivos anexados a esta movimentação
     */
    public function files()
    {
        return $this->hasMany(StockMovementFile::class, 'stock_movement_id');
    }

    /**
     * Filtra movimentações por tipo ('entrada' ou 'saida')
     */
    public function scopeTipo($query, $tipo)
    {
        return $query->where('movement_type', $tipo);
    }

    /**
     * Exemplo de evento de boot para logs personalizados
     */
    protected static function booted()
    {
        static::created(function ($movement) {
            // Lógica extra pós-criação, se necessário
        });
    }
}