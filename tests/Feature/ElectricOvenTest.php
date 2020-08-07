<?php

namespace Tests\Feature;

use App\Utilities\Ovens\ElectricOven;
use Tests\TestCase;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Utilities\Pizza;

class ElectricOvenTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations;

    /** @var ElectricOven */
    private $oven;

    /**
     * Sets up the test
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->oven = new ElectricOven();
    }

    /**
     * Tests the heatUp method
     *
     * @return void
     */
    public function testHeatUp(): void
    {
        $this->expectOutputString('10 minutes to heat' . PHP_EOL);

        $this->oven->heatUp();
    }

    /**
     * Tests the bake method
     *
     * @return void
     */
    public function testBake(): void
    {
        $this->expectOutputString('7 minutes to bake pizza' . PHP_EOL);

        $pizza = new Pizza(Recipe::find(Recipe::MARGHERITA_ID)->first());

        $this->oven->bake($pizza);
    }

    /**
     * Tests the turnOff method
     *
     * @return void
     */
    public function testTurnOff(): void
    {
        $this->expectOutputString('oven is off' . PHP_EOL);

        $this->oven->turnOff();
    }
}
