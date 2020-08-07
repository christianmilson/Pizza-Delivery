<?php

namespace Tests\Feature;

use App\Utilities\Fridge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Ingredient;
use Tests\TestCase;

class FridgeTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations;

    /** @var Fridge */
    private $fridge;

    /**
     * Sets up the test
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->fridge = new Fridge();
    }

    /**
     * Tests the has method
     *
     * @return void
     */
    public function testHas(): void
    {
        $ingredient = Ingredient::find(Ingredient::TOMATO_ID)->first();

        $this->assertFalse($this->fridge->has($ingredient, 5));
        $this->assertEquals($this->fridge->amount($ingredient), 0);

        $this->fridge->add($ingredient, 5);

        $this->assertTrue($this->fridge->has($ingredient, 5));
        $this->assertEquals($this->fridge->amount($ingredient), 5);

        $this->fridge->take($ingredient, 5);

        $this->assertFalse($this->fridge->has($ingredient, 5));
        $this->assertEquals($this->fridge->amount($ingredient), 0);
    }
}
