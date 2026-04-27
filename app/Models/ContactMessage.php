<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_submission_id',
        'sender_type',
        'sender_name',
        'sender_email',
        'body',
        'emailed_at',
    ];

    protected $casts = [
        'emailed_at' => 'datetime',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(ContactSubmission::class, 'contact_submission_id');
    }
}
