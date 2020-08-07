<?php

namespace App\Utilities\PriceModels;

use App\Models\Recipe;
use App\Models\Order;

class Standard implements Model
{
    /**
     * Calculates the price of a standard pizza
     *
     * @param Recipe $recipe
     * @param Order $order
     *
     * @return float
     */
    public function calculate(Recipe $recipe, Order $order): float
    {
        $price = 0.0;

        $price += $recipe->price;

        return $price;
    }
}
