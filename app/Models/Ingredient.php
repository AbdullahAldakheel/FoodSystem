<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'weight_in_grams'];

    /**
     * @description Ingredient can be used in many products through a pivot table (product_ingredient)
     * common column is weight_in_grams
     */
    public function productIngredients(): hasMany
    {
        return $this->hasMany(ProductIngredient::class);
    }


    /**
     * @description Sum of all the weights of the ingredient used in all the products
     */
    public function getOrderedWeightSumAttribute(): float
    {
        return $this->productIngredients->sum(function ($productIngredient) {
            return $productIngredient->weight_in_grams * $productIngredient->product->orders->count();
        });
    }
}
