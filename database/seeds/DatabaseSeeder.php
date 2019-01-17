<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
     
    public function run()
    {
        $this->call(PizzasTableSeeder::class);
        $this->call(InfosTableSeeder::class);
    }
}
