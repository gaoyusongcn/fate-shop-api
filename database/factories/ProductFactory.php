<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Product;
use App\Models\ProductCategory;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Product::class, function (Faker $faker) {
    $image = $faker->randomElement([
        "https://cdn.learnku.com/uploads/images/201806/01/5320/7kG1HekGK6.jpg",
        "https://cdn.learnku.com/uploads/images/201806/01/5320/1B3n0ATKrn.jpg",
        "https://cdn.learnku.com/uploads/images/201806/01/5320/r3BNRe4zXG.jpg",
        "https://cdn.learnku.com/uploads/images/201806/01/5320/C0bVuKB2nt.jpg",
        "https://cdn.learnku.com/uploads/images/201806/01/5320/82Wf2sg8gM.jpg",
        "https://cdn.learnku.com/uploads/images/201806/01/5320/nIvBAQO5Pj.jpg",
        "https://cdn.learnku.com/uploads/images/201806/01/5320/XrtIwzrxj7.jpg",
        "https://cdn.learnku.com/uploads/images/201806/01/5320/uYEHCJ1oRp.jpg",
        "https://cdn.learnku.com/uploads/images/201806/01/5320/2JMRaFwRpo.jpg",
        "https://cdn.learnku.com/uploads/images/201806/01/5320/pa7DrV43Mw.jpg",
    ]);

    // 从数据库中随机取一个类目
    $category = ProductCategory::query()->where('is_directory', false)->inRandomOrder()->first();

    return [
        'type'         => $faker->randomElement(array_keys(Product::$typeMap)),
        'title'        => $faker->word,
        'long_title'   => $faker->sentence,
        'description'  => $faker->sentence,
        'image'        => $image,
        'on_sale'      => true,
        'rating'       => $faker->numberBetween(0, 5),
        'sold_count'   => 0,
        'review_count' => 0,
        'price'        => 0,
        'category_id'  => $category ? $category->id : null,
    ];
});