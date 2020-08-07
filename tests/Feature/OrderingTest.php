<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderRecipe;
use App\Models\Recipe;
use App\Models\RecipeType;
use App\Models\RecipeIngredient;
use App\Utilities\Luigis;
use App\Utilities\Pizza;
use BadFunctionCallException;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\RecipeAmendment;
use App\Models\Ingredient;

class OrderingTest extends TestCase
{
    use RefreshDatabase, DatabaseMigrations;

    /** @var Luigis */
    private $luigis;

    /**
     * Sets up the test
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->luigis = new Luigis();
    }

    /**
     * DataProvider for testNormalPizza
     *
     * @return array
     */
    public function testNormalPizzaDataProvider(): array
    {
        return [
            [
                'inputs' => [
                    'recipes' => [
                        Recipe::MARGHERITA_ID,
                    ],
                ],
                'outputs' => [
                    'price' => 6.99,
                ],
            ],
            [
                'inputs' => [
                    'recipes' => [
                        Recipe::MARGHERITA_ID,
                        Recipe::HAWAIIAN_ID,
                    ],
                ],
                'outputs' => [
                    'price' => 15.98,
                ],
            ],
        ];
    }

    /**
     * Tests adding a normal pizza
     *
     * @param array $input
     * @param array $output
     *
     * @dataProvider testNormalPizzaDataProvider
     * @return void
     */
    public function testNormalPizza($input, $output): void
    {

        // 1) Create the order
        $order = Order::create([
            'status' => Order::STATUS_PENDING
        ]);

        foreach ($input['recipes'] as $recipe) {
            OrderRecipe::create([
                'order_id' => $order->id,
                'recipe_id' => $recipe,
            ]);
        }

        $this->assertEquals(count($input['recipes']), count($order->recipes));

        foreach ($order->recipes as $key => $recipe) {
            $this->assertEquals($input['recipes'][$key], $order->recipes[$key]->id);
        }

        $this->assertEquals($output['price'], $order->getPriceAttribute());


        // 2) Deliver the order
        $pizzas = $this->luigis->deliver($order);
        $this->assertEquals(count($input['recipes']), count($pizzas));


        // 3) Verify the order
        /** @var Pizza $pizza */
        $pizza = $pizzas->first();
        $this->assertEquals('Margherita', $pizza->getName());
        $this->assertEquals(Pizza::STATUS_COOKED, $pizza->getStatus());

        // 4) Eat the pizza
        $pizza->eatSlice();

        $this->assertEquals(7, $pizza->getSlicesRemaining());
        $this->assertEquals(Pizza::STATUS_PARTLY_EATEN, $pizza->getStatus());

        while ($pizza->getSlicesRemaining()) {
            $pizza->eatSlice();
        }
        $this->assertEquals(Pizza::STATUS_ALL_EATEN, $pizza->getStatus());


        // 5) Verify can't eat an eaten pizza
        $gotException = 'no exception thrown';
        try {
            $pizza->eatSlice();
        } catch (BadFunctionCallException $e) {
            $gotException = 'exception was thrown';
        }
        $this->assertEquals('exception was thrown', $gotException);
    }

    /**
     * DataProvider for testAmend
     *
     * @return array
     */
    public function testAmendDataProvider(): array
    {
        return [
            [
                'inputs' => [
                    'original' => Recipe::MARGHERITA_ID,
                    'amendments' => [
                        Ingredient::TOMATO_ID => 1,
                        Ingredient::MOZZARELLA_ID => -1,
                    ],
                ],
                'outputs' => [
                    'price' => 7.09,
                    'name' => 'Margherita',
                ],
            ],
            [
                'inputs' => [
                    'original' => Recipe::HAWAIIAN_ID,
                    'amendments' => [
                        Ingredient::TOMATO_ID => 1,
                        Ingredient::PINEAPPLE_ID => 10,
                    ],
                ],
                'outputs' => [
                    'price' => 13.09,
                    'name' => 'Hawaiian',
                ],
            ],
        ];
    }

    /**
     * Test Amend a pizza
     *
     * @param array $input
     * @param array $output
     *
     * @dataProvider testAmendDataProvider
     */
    public function testAmend($input, $output): void
    {
        $original = Recipe::find($input['original']);

        // 1) Create the order
        $order = Order::create([
            'status' => Order::STATUS_PENDING
        ]);

        // 2) Create amended recipe (based on standard)
        $amendRecipe = Recipe::create([
            'name'      => $original->name,
            'price'     => $original->price,
            'type_id'   => RecipeType::TYPE_ALTER,
        ]);

        OrderRecipe::create([
            'order_id' => $order->id,
            'recipe_id' => $amendRecipe->id,
        ]);

        // 3) Get the standard ingredients
        $recipe = [];
        $baseIngredients = $original->ingredientRequirements;
        foreach ($baseIngredients as $baseIngredient) {
            $recipe[$baseIngredient->ingredient_id] = $baseIngredient->amount;
        }

        // 4) Get the amendements, and alter the standard ingredients
        foreach ($input['amendments'] as $ingredient => $amendment) {
            RecipeAmendment::create([
                'order_id' => $order->id,
                'ingredient_id' => $ingredient,
                'amendment' => $amendment,
            ]);

            if ($recipe[$ingredient]) {
                $recipe[$ingredient] += $amendment;
            } else {
                $recipe[$ingredient] = $amendment;
            }
        }

        // 5) Insert new recipie into the receipe_ingredients table
        foreach ($recipe as $ingredient => $amount) {
            RecipeIngredient::create([
                'recipe_id' => $amendRecipe->id,
                'ingredient_id' => $ingredient,
                'amount' => $amount,
            ]);
        }

        $this->assertEquals($output['price'], $order->getPriceAttribute());

        $pizzas = $this->luigis->deliver($order);
        $pizza = $pizzas->first();

        $this->assertEquals(1, count($pizzas));
        $this->assertEquals($output['name'], $pizza->getName());
    }

    /**
     * Data Provider for testCustomPizza
     *
     * @return array
     */
    public function testCustomPizzaDataProvider(): array
    {
        return [
            [
                'inputs' => [
                    'name' => 'Custom1',
                    'ingredients' => [
                        Ingredient::TOMATO_ID => 5,
                        Ingredient::MOZZARELLA_ID => 3,
                    ],
                ],
                'outputs' => [
                    'price' => 7.1,
                ],
            ],
            [
                'inputs' => [
                    'name' => 'Custom1',
                    'ingredients' => [
                        Ingredient::TOMATO_ID => 2,
                        Ingredient::MOZZARELLA_ID => 5,
                        Ingredient::HAM_ID => 1,
                    ],
                ],
                'outputs' => [
                    'price' => 7.5,
                ],
            ],
            [
                'inputs' => [
                    'name' => 'Custom1',
                    'ingredients' => [],
                ],
                'outputs' => [
                    'price' => 6.0,
                ],
            ],
        ];
    }

    /**
     * Create Custom Pizza Test
     *
     * @param array $input
     * @param array $output
     *
     * @dataProvider testCustomPizzaDataProvider
     * @return void
     */
    public function testCustomPizza($input, $output): void
    {
        // 1) Create the order
        $order = Order::create([
            'status' => Order::STATUS_PENDING,
        ]);

        // 2) Create the custom recipe
        $recipe = Recipe::create([
            'name' => 'Custom' . uniqid(),
            'price' => 0,
            'type_id' => RecipeType::TYPE_CUSTOM,
        ]);

        OrderRecipe::create([
            'order_id' => $order->id,
            'recipe_id' => $recipe->id,
        ]);

        // 3) Add ingredients to the recipe_ingredients table
        foreach ($input['ingredients'] as $ingredient => $amount) {
            RecipeIngredient::create([
                'recipe_id' => $recipe->id,
                'ingredient_id' => $ingredient,
                'amount' => $amount,
            ]);
        }

        $this->assertEquals($output['price'], $order->getPriceAttribute());
    }
}
