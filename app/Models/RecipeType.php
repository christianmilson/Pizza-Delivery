<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;

class RecipeType extends Model
{
    const TYPE_STANDARD = 1;
    const TYPE_ALTER = 2;
    const TYPE_CUSTOM = 3;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'luigis_recipe_type';

    /**
     * Disable timestamps
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Fillable fields
     *
     * @var array
     */
    protected $fillable = ['id', 'class'];
}
