<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PageSection extends Model
{
    protected $fillable = [
        'page_id',
        'anchor',
        'nav_label',
        'heading',
        'body',
        'background_image_path',
        'background_position',
        'sort_order',
        'cta_label',
        'cta_url',
    ];

    protected $appends = [
        'background_image_url',
    ];

    /** @return BelongsTo<Page, PageSection> */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function getBackgroundImageUrlAttribute(): ?string
    {
        $path = $this->background_image_path;

        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }
}
