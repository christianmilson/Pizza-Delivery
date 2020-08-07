<?php

namespace App\Utilities;

use BadFunctionCallException;
use App\Models\Recipe;
use InvalidArgumentException;

class Pizza
{
    const STATUS_RAW = 'raw';
    const STATUS_COOKED = 'cooked';
    const STATUS_OVER_COOKED = 'overCooked';
    const STATUS_PARTLY_EATEN = 'partlyEaten';
    const STATUS_ALL_EATEN = 'allEaten';
    const STATUSES = [
        self::STATUS_RAW,
        self::STATUS_COOKED,
        self::STATUS_OVER_COOKED,
        self::STATUS_PARTLY_EATEN,
        self::STATUS_ALL_EATEN,
    ];

    private $slicesRemaining = 8;
    /** @var Recipe */
    private $recipe;
    private $status = '';

    public function __construct(Recipe $recipe)
    {
        $this->recipe = $recipe;
        $this->status = self::STATUS_RAW;
    }

    /**
     * Method handles logic when a slice of pizza is eaten
     *
     * @throws BadFunctionCallException if no slices left to eat
     * @throws BadFunctionCallException if trying to eat a raw pizza
     * @return void
     */
    public function eatSlice(): void
    {
        if ($this->status === self::STATUS_RAW) {
            throw new BadFunctionCallException('pizza is raw !');
        }

        if ($this->slicesRemaining <= 0) {
            throw new BadFunctionCallException('pizza is already eaten');
        }

        $this->slicesRemaining -= 1;

        if ($this->slicesRemaining > 0) {
            $this->setStatus(self::STATUS_PARTLY_EATEN);
        } else {
            $this->setStatus(self::STATUS_ALL_EATEN);
        }
    }

    public function getSlicesRemaining(): int
    {
        return $this->slicesRemaining;
    }

    public function getRecipe(): Recipe
    {
        return $this->recipe;
    }

    public function getName(): string
    {
        return $this->recipe->name;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): Pizza
    {
        if (!in_array($status, self::STATUSES)) {
            throw new InvalidArgumentException("$status is not a valid status");
        }
        $this->status = $status;
        return $this;
    }
}
