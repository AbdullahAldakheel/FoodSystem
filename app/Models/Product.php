<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * @description Product can have many ingredients through a pivot table (product_ingredient)
     */
    public function productIngredients(): HasMany
    {
        return $this->hasMany(ProductIngredient::class);
    }

    /**
     * @description Product can have many orders
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
