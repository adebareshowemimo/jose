<?php

namespace App\Support;

class Currency
{
    public const ALLOWED = ['NGN', 'USD'];

    public const SYMBOLS = [
        'NGN' => '₦',
        'USD' => '$',
    ];

    public const FALLBACK_DEFAULT = 'NGN';
    public const FALLBACK_USD_TO_NGN = 1500.00;

    public static function default(): string
    {
        $code = strtoupper((string) setting('currency.default', self::FALLBACK_DEFAULT));
        return in_array($code, self::ALLOWED, true) ? $code : self::FALLBACK_DEFAULT;
    }

    public static function usdToNgnRate(): float
    {
        $rate = (float) setting('currency.usd_to_ngn_rate', self::FALLBACK_USD_TO_NGN);
        return $rate > 0 ? $rate : self::FALLBACK_USD_TO_NGN;
    }

    public static function rate(string $from, string $to): float
    {
        $from = strtoupper($from);
        $to   = strtoupper($to);

        if ($from === $to) {
            return 1.0;
        }

        $usdToNgn = self::usdToNgnRate();

        if ($from === 'USD' && $to === 'NGN') return $usdToNgn;
        if ($from === 'NGN' && $to === 'USD') return 1 / $usdToNgn;

        return 1.0;
    }

    public static function convert(float $amount, string $from, string $to): float
    {
        return self::rate($from, $to) * $amount;
    }

    public static function convertToDefault(float $amount, string $from): float
    {
        return self::convert($amount, $from, self::default());
    }

    public static function symbol(string $code): string
    {
        $code = strtoupper($code);
        return self::SYMBOLS[$code] ?? ($code . ' ');
    }

    public static function format(float $amount, ?string $currency = null): string
    {
        $source = strtoupper($currency ?: self::default());
        $target = self::default();

        $value = $source === $target
            ? $amount
            : self::convert($amount, $source, $target);

        return self::symbol($target) . number_format(round($value, 2), 2);
    }
}
