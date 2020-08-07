<?php

use Illuminate\Database\Seeder;
use App\Models\Ingredient;

class IngredientTableSeeder extends Seeder
{

    public function run()
    {
        Ingredient::updateOrCreate(
            ['id' => Ingredient::TOMATO_ID],
            [
                'name' => 'Tomato',
                'price' => 10
            ],
        );

        Ingredient::updateOrCreate(
            ['id' => Ingredient::MOZZARELLA_ID],
            [
                'name' => 'Mozzarella',
                'price' => 20
            ],
        );

        Ingredient::updateOrCreate(
            ['id' => Ingredient::HAM_ID],
            [
                'name' => 'Ham',
                'price' => 30
            ],
        );

        Ingredient::updateOrCreate(
            ['id' => Ingredient::PINEAPPLE_ID],
            [
                'name' => 'Pineapple',
                'price' => 40
            ],
        );
    }
}
