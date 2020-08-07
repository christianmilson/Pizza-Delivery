<?php

namespace Tests\Feature;

use App\Utilities\Pizza;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use BadFunctionCallException;
use App\Models\Recipe;

class PizzaTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations;

    /** @var Pizza */
    private $pizza;

    /**
     * Sets up the test
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->pizza = new Pizza(Recipe::find(Recipe::MARGHERITA_ID)->first());
    }

    /**
     * DataProvider for testEatSlice
     *
     * @return array
     */
    public function testEatSliceDataProvider(): array
    {
        return [
            [
                'input' => [
                    'initial'   => Pizza::STATUS_RAW,
                    'bites'     => 1,
                ],
                'output' => [
                    'status' => Pizza::STATUS_RAW,
                    'exception' => true,
                ],
            ],
            [
                'input' => [
                    'initial'   => Pizza::STATUS_COOKED,
                    'bites'     => 1,
                ],
                'output' => [
                    'status' => Pizza::STATUS_PARTLY_EATEN,
                    'exception' => false,
                ],
            ],
            [
                'input' => [
                    'initial'   => Pizza::STATUS_COOKED,
                    'bites'     => 8,
                ],
                'output' => [
                    'status' => Pizza::STATUS_ALL_EATEN,
                    'exception' => false,
                ],
            ],
            [
                'input' => [
                    'initial'   => Pizza::STATUS_COOKED,
                    'bites'     => 9,
                ],
                'output' => [
                    'status' => Pizza::STATUS_ALL_EATEN,
                    'exception' => true,
                ],
            ],
        ];
    }

    /**
     * Tests the eatSlice method
     *
     * @param array $input
     * @param array $output
     *
     * @dataProvider testEatSliceDataProvider
     * @return void
     */
    public function testEatSlice($input, $output): void
    {
        $this->pizza->setStatus($input['initial']);

        if ($output['exception']) {
            $this->expectException(BadFunctionCallException::class);
        }

        for ($i = 0; $i < $input['bites']; $i++) {
            $this->pizza->eatSlice();
        }

        $this->assertEquals($this->pizza->getStatus(), $output['status']);
    }
}
