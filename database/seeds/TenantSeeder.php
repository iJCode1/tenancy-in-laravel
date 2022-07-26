<?php

use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder{
  /**
   * Run the database seeds.
   * 
   * @return void
   */

   public function run(){
    factory(\App\Models\Tenant\Product::class, 2)->create();
   }
}