<?php

namespace App\Utilities;

use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Recipe;
use Illuminate\Support\Collection;
use App\Utilities\Ovens\ElectricOven;
use App\Utilities\Pizza;

class Luigis
{
    /** @var Fridge */
    private $fridge;
    /** @var Oven */
    private $oven;

    public function __construct(Oven $oven = null)
    {
        $this->fridge = new Fridge();
        $this->oven = $oven ? $oven : new ElectricOven();

        // Heat up oven when we create luigis -> to save time when orders start arriving :)
        $this->oven->heatUp();
    }

    public function restockFridge(): void
    {
        /** @var Ingredient $ingredient */
        foreach (Ingredient::all() as $ingredient) {
            $this->fridge->add($ingredient, 10);
        }
    }

    /**
     * Method overseas the preparation and cooking process
     * before delivering the completed pizza
     *
     * @param Order $order
     * @return Pizza[]|Collection
     */
    public function deliver(Order $order): Collection
    {
        $pizzas = [];

        // prepare and cook each recipe in the order
        foreach ($order->recipes as $recipe) {
            $pizza = $this->prepare($recipe);
            $this->cook($pizza);

            $pizzas[] = $pizza;
        }

        return collect($pizzas);
    }

    /**
     * Method builds the pizza using available ingredients from the fridge
     *
     * @param Recipe $recipe
     *
     * return Pizza
     */
    private function prepare(Recipe $recipe): Pizza
    {
        // Get ingredient list
        $ingredients = $recipe->ingredientRequirements;

        foreach ($ingredients as $ingredient) {
            // 1) Check fridge has enough of each ingredient
            if ($this->fridge->has($ingredient->ingredient, $ingredient->amount) === false) {
                // 2) restockFridge if needed
                $this->fridge->add($ingredient->ingredient, $ingredient->amount);
            }

            // 3) take ingredients from the fridge
            $this->fridge->take($ingredient->ingredient, $ingredient->amount);
        }

        // 4) create new Pizza
        return new Pizza($recipe);
    }

    /**
     * Method overseas the cooking process
     *
     * @param Pizza $pizza
     *
     * @return void
     */
    private function cook(Pizza &$pizza): void
    {
        // Bakes the pizza in specified oven
        $this->oven->bake($pizza);

        // Sets the pizza Status to cooked
        $pizza->setStatus('cooked');
    }
}
