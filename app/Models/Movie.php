<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Movie extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'deleted_at' => 'datetime',
    ];
    protected $fillable = [
        'title',
        'genre_code',
        'year',
        'poster_filename',
        'synopsis',
        'trailer_url',
    ];

    public function screenings(): HasMany
    {
        return $this->hasMany(Screening::class);
    }

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class, 'genre_code', 'code');
    }

    public function getPoster()
    {
        if ($this->poster_filename && Storage::exists("public/posters/{$this->poster_filename}")) {
            return asset("storage/posters/{$this->poster_filename}");
        } else {
            return asset("img/no_poster_1.png");
        }
    }

    public function getImageExistsAttribute()
    {
        return Storage::exists("public/posters/{$this->poster_filename}");
    }

    public function getScreenings()
    {
        $screenings = $this->screenings
            ->where('date', '>=', date('Y-m-d', strtotime(today())))
            ->where('date', '<=', date('Y-m-d', strtotime(today()->addWeeks(2))));
        return $screenings;
    }

    public function getTrailerEmbedUrl()
    {
        $fullUrl = $this->trailer_url;
        $videoID = substr($fullUrl, strpos($fullUrl, "watch?v=") + 8, 11);

        return "https://www.youtube.com/embed/" . $videoID . "?";
    }

}
