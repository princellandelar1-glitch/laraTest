<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Relationship;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Models\Book;
use App\Models\Publisher;

class Publisher extends Model
{
    public $timestamps = false;

    protected $fillable = ['name'];

    // defines the many relationship with the book model
    public function books(): HasMany 
    {
        return $this->hasMany(Book::class);
    }
}
