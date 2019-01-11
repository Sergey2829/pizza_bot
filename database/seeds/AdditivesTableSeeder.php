<?php

use Illuminate\Database\Seeder;

class AdditivesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('additives')->insert([
            'name' => 'Бекон',
            'price' => '20',
        ]);
        DB::table('additives')->insert([
            'name' => 'Ветчина',
            'price' => '20',
        ]);
        DB::table('additives')->insert([
            'name' => 'Грибы',
            'price' => '15',
        ]);
        DB::table('additives')->insert([
            'name' => 'Перец',
            'price' => '15',
        ]);
        DB::table('additives')->insert([
            'name' => 'Сыр',
            'price' => '15',
        ]);
    }
}
