<?php

namespace App\Utilities;

use App\Utilities\PriceModels\Model;
use App\Models\Order;
use App\Models\Recipe;

class PriceModel
{
    /**
     * Method finds the relevant price model class for the recipe in the order
     *
     * @param Order $order
     *
     * @return float
     */
    public static function generate(Order $order): float
    {
        $price = 0.0;

        foreach ($order->recipes as $recipe) {
            // Get the price model class for type
            $model = $recipe->type->class;
            $price += self::calculate(new $model(), $recipe, $order);
        }

        return $price;
    }

    /**
     * Call calulates method in relevant price model
     *
     * @param Model $model
     * @param Recipe $recipe
     * @param Order $order
     *
     * @return float
     */
    protected static function calculate(Model $model, Recipe $recipe, Order $order): float
    {
        return $model->calculate($recipe, $order);
    }
}
