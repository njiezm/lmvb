<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    protected $fillable = ['name', 'slug', 'starts_on', 'ends_on', 'is_current'];

    protected $casts = [
        'starts_on' => 'date',
        'ends_on' => 'date',
        'is_current' => 'boolean',
    ];

    public function competitions()
    {
        return $this->hasMany(Competition::class)->orderBy('sort_order')->orderBy('name');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Saison sportive en cours au format FFVolley (bascule au 1er août). */
    public static function currentName(?CarbonImmutable $date = null): string
    {
        $date ??= CarbonImmutable::now();
        $start = $date->month >= 8 ? $date->year : $date->year - 1;

        return $start.'/'.($start + 1);
    }

    public static function current(): ?self
    {
        return static::where('is_current', true)->first()
            ?? static::orderByDesc('name')->first();
    }

    public static function findOrCreateByName(string $name): self
    {
        [$start, $end] = array_map('intval', explode('/', $name));

        return static::firstOrCreate(['name' => $name], [
            'slug' => str_replace('/', '-', $name),
            'starts_on' => "$start-08-01",
            'ends_on' => "$end-07-31",
        ]);
    }
}
