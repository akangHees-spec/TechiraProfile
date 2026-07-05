<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Image\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Service extends Model implements Sortable, HasMedia
{
    use HasSlug, InteractsWithMedia, SortableTrait;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'icon',
        'short_description',
        'description',
        'is_featured',
        'whatsapp_click_count',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'whatsapp_click_count' => 'integer',
        'order' => 'integer',
    ];

    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function features(): HasMany
    {
        return $this->hasMany(ServiceFeature::class);
    }

    protected function whatsappLink(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Get WhatsApp number from Category override, otherwise fallback to Settings
                $number = $this->category?->whatsapp_number;
                if (empty($number)) {
                    $number = Setting::where('key', 'whatsapp_number')->value('value');
                }

                // Fallback email/phone check clean up
                $number = preg_replace('/[^0-9]/', '', $number ?? '');

                // Ensure it starts with country code (e.g. 62 if 08...)
                if (str_starts_with($number, '0')) {
                    $number = '62' . substr($number, 1);
                }

                // Get message template
                $template = Setting::where('key', 'whatsapp_message_template')->value('value');
                if (empty($template)) {
                    $template = "Halo, saya tertarik dengan layanan {name}. Info selengkapnya: {url}";
                }

                $serviceUrl = url('/services/' . $this->slug);
                $message = str_replace(
                    ['{name}', '{url}'],
                    [$this->name, $serviceUrl],
                    $template
                );

                return "https://wa.me/{$number}?text=" . rawurlencode($message);
            }
        );
    }
}
