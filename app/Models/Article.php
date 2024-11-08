<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'category_id' => 'integer',
        'user_id' => 'string',
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeYear(Builder $query, $value)
    {
        $query->whereYear('created_at', $value);
    }

    public function scopeMonth(Builder $query, $value)
    {
        $query->whereMonth('created_at', $value);
    }

    public function scopeCategories(Builder $query, $categories)
    {
        $query->whereHas('category', function (Builder $query) use ($categories) {
            $categoriesSlug = explode(',', $categories);
            //estoy no funcionaria por que el where me agregarian un and
            /*foreach ($categories as $category) {
                $query->whereSlug($category);
            }*/
            $query->whereIn('slug', $categoriesSlug);
        });
    }
    public function scopeAuthors(Builder $query, $authors)
    {
        $query->whereHas('author', function (Builder $query) use ($authors) {
            $authorNames = explode(',', $authors);
            $query->whereIn('name', $authorNames);
        });
    }
}
