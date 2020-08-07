<?php

namespace App\Utilities\Ovens;

use App\Utilities\Oven;
use App\Utilities\Pizza;

/**
 * Electric Oven class
 */
class ElectricOven implements Oven
{
    const MINIMUM_BAKE_TIME = 5;

    /**
     * Method simulates the heating up of oven
     *
     * @return self
     */
    public function heatUp(): self
    {
        echo '10 minutes to heat' . PHP_EOL;

        return $this;
    }

    /**
     * Method simulates baking of pizza
     *
     * @return self
     */
    public function bake(Pizza &$pizza): self
    {
        $ingredientTypes = empty($pizza->getRecipe()->ingredientRequirements)
                            ? 0 : count($pizza->getRecipe()->ingredientRequirements);

        echo (self::MINIMUM_BAKE_TIME + $ingredientTypes) . ' minutes to bake pizza' . PHP_EOL;

        return $this;
    }

    /**
     * Method simulates turning off oven
     *
     * @return self
     */
    public function turnOff(): self
    {
        echo 'oven is off' . PHP_EOL;

        return $this;
    }
}
