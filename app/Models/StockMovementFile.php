<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovementFile extends Model
{
    protected $fillable = [
        'stock_movement_id',
        'file_path',
        'original_name',
        'extension',
        'mime_type',
        'size',
        // outros campos se quiser
    ];

    public function movement()
    {
        return $this->belongsTo(\App\Models\InventoryMovement::class, 'stock_movement_id');
    }
}