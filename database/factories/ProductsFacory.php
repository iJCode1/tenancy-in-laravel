<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Tenant\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker){
  return [
    "name" => $faker->name,
    "price" => $faker->randomFloat(),
    "created_at" => now()
  ];
});