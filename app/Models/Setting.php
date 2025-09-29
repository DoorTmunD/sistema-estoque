<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public $timestamps = true;

    // Helper: pegar valor da chave (já faz cast se json)
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) return $default;
        $val = $setting->value;
        // Cast para array se json
        return is_string($val) && (str_starts_with($val, '{') || str_starts_with($val, '[')) ? json_decode($val, true) : $val;
    }

    // Helper: salvar valor
    public static function set($key, $value)
    {
        static::updateOrCreate(['key' => $key], ['value' => is_array($value) ? json_encode($value) : $value]);
    }
}