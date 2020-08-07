<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;
use App\Models\RecipeType;

/**
 * Class Recipe
 * @package App\Models
 * @mixin Builder
 *
 * @property int $id
 * @property string $name
 * @property float $price
 *
 * @property-read Collection|Ingredient[] $ingredients
 * @property-read Collection|RecipeIngredient[] $ingredientRequirements
 * @property-read Collection|Order[] $orders
 */
class Recipe extends Model
{
    const MARGHERITA_ID = 1;
    const HAWAIIAN_ID = 2;

    protected $table = 'luigis_recipes';
    public $timestamps = false;
    protected $fillable = ['id', 'name', 'price', 'type_id'];

    public function ingredients(): HasManyThrough
    {
        return $this->hasManyThrough(
            Ingredient::class,
            RecipeIngredient::class,
            'recipe_id',
            'id',
            'id',
            'ingredient_id'
        );
    }

    public function ingredientRequirements(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function orders(): HasManyThrough
    {
        return $this->hasManyThrough(
            Order::class,
            OrderRecipe::class,
            'recipe_id',
            'id',
            'id',
            'order_id'
        );
    }

    public function type()
    {
        return $this->hasOne(RecipeType::class, 'id', 'type_id');
    }
}
