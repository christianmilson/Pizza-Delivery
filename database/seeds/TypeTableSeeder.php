<?php

use Illuminate\Database\Seeder;
use App\Models\RecipeType;

class TypeTableSeeder extends Seeder
{

    public function run()
    {
        RecipeType::updateOrCreate(
            ['id' => 1],
            [
                'class'  => '\App\Utilities\PriceModels\Standard',
                'order'  => '\App\Utilities\Orders\Standard',
            ]
        );

        RecipeType::updateOrCreate(
            ['id' => 2],
            [
                'class'  => '\App\Utilities\PriceModels\Amendment',
                'order'  => '\App\Utilities\Orders\Standard',
            ]
        );

        RecipeType::updateOrCreate(
            ['id' => 3],
            [
                'class'  => '\App\Utilities\PriceModels\Custom',
                'order'  => '\App\Utilities\Orders\Standard',
            ]
        );
    }
}
