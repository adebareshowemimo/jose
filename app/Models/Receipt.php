<?php

namespace App\Models;

use App\Support\Settings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receipt extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'payment_id', 'order_id', 'user_id', 'number',
        'amount', 'currency', 'issued_at', 'issued_by_admin_id',
        'last_emailed_at', 'last_emailed_to', 'notes', 'pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'issued_at' => 'datetime',
            'last_emailed_at' => 'datetime',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by_admin_id');
    }

    public static function nextNumberFor(Payment $payment): string
    {
        $prefix = (string) (app(Settings::class)->get('receipt.number_prefix') ?? 'RCP-');
        return $prefix . str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT);
    }
}
