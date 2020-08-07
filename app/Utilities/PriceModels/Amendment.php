<?php

namespace App\Utilities\PriceModels;

use App\Models\Recipe;
use App\Models\Order;

class Amendment implements Model
{
    /**
     * Calculates the price of an amended standard pizza
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

        // We only care about added toppings
        $amendments = $order->amendments()->where('amendment', '>', 0)->get();
        foreach ($amendments as $amendment) {
            $n = $amendment->amendment;
            $x = $amendment->ingredient->price;

            $price += (($n * $x) / 100);
        }

        return $price;
    }
}
