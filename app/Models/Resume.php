<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Resume extends Model
{
    use SoftDeletes;

    protected $fillable = ['candidate_id', 'title', 'file_path', 'is_default'];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    /**
     * Public URL to the CV file. CV-Manager uploads are moved straight into
     * public/uploads/resumes (served directly), whereas job-application uploads
     * use the public storage disk (storage/app/public via the /storage symlink),
     * so resolve to whichever actually holds the file.
     */
    public function url(): ?string
    {
        if (empty($this->file_path)) {
            return null;
        }

        if (is_file(public_path($this->file_path))) {
            return asset($this->file_path);
        }

        return Storage::disk('public')->url($this->file_path);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }
}
