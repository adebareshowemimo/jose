<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ContactSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'category',
        'message',
        'status',
        'priority',
        'reply_token',
        'last_responded_at',
        'closed_at',
    ];

    protected $casts = [
        'last_responded_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (ContactSubmission $submission) {
            $submission->reply_token ??= Str::random(48);
        });
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ContactMessage::class)->latest();
    }

    public function chronologicalMessages(): HasMany
    {
        return $this->hasMany(ContactMessage::class)->oldest();
    }
}
