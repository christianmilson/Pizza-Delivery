<?php

namespace App\Utilities\PriceModels;

use App\Models\Recipe;
use App\Models\Order;

/**
 * Interface that all price models must accept contract of
 */
interface Model
{
    /**
     * Calulcate implementation for Recipe type
     *
     * @return self
     */
    public function calculate(Recipe $recipe, Order $order): float;
}
