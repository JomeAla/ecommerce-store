<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Setting extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return match ($setting->type) {
            'number' => (float) $setting->value,
            'integer' => (int) $setting->value,
            'boolean' => (bool) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    public static function set(string $key, mixed $value, ?string $group = null, string $type = 'text'): self
    {
        $setting = static::firstOrNew(['key' => $key]);

        $setting->value = match ($type) {
            'json' => json_encode($value),
            default => $value,
        };

        $setting->group = $group;
        $setting->type = $type;
        $setting->is_public = false;

        $setting->save();

        return $setting;
    }

    public static function allPublic(): Collection
    {
        return static::where('is_public', true)->get();
    }

    public static function byGroup(string $group): Collection
    {
        return static::where('group', $group)->get();
    }

    public static function forget(string $key): bool
    {
        return static::where('key', $key)->delete() > 0;
    }
}