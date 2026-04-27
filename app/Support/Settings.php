<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class Settings
{
    private const CACHE_KEY = 'app.settings.all';

    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            try {
                return Setting::all()->mapWithKeys(fn ($s) => [$s->key => $s->value])->all();
            } catch (\Throwable $e) {
                return [];
            }
        });
    }

    public function group(string $group): array
    {
        try {
            return Setting::where('group', $group)
                ->get()
                ->mapWithKeys(fn ($s) => [$s->key => $s->value])
                ->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function get(string $key, $default = null)
    {
        $all = $this->all();
        return array_key_exists($key, $all) && $all[$key] !== null ? $all[$key] : $default;
    }

    public function set(string $key, $value, string $group = 'general', bool $encrypted = false): void
    {
        $row = Setting::firstOrNew(['key' => $key]);
        $row->group = $group;
        $row->is_encrypted = $encrypted;
        $row->value = $value;
        $row->save();
        $this->flush();
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
