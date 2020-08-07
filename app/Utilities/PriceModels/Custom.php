<?php

namespace App\Utilities\PriceModels;

use App\Models\Recipe;
use App\Models\Order;

class Custom implements Model
{
    const BASE_PRICE = 6.00;

    /**
     * Calculates the price of a custom pizza
     *
     * @param Recipe $recipe
     * @param Order $order
     *
     * @return float
     */
    public function calculate(Recipe $recipe, Order $order): float
    {
        $price = 0.0;

        foreach ($recipe->ingredientRequirements as $ingredient) {
            $x = $ingredient->ingredient->price;
            $n = $ingredient->amount;

            $price += (($x * $n) / 100);
        }

        return self::BASE_PRICE + $price;
    }
}
