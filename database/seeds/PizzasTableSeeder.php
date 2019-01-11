<?php

use Illuminate\Database\Seeder;

class PizzasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pizzas')->insert([
            'name' => 'Маргарита',
            'photo' => 'https://grill-wine.com/wp-content/uploads/2016/01/margarita.jpg',
            'description' =>  'Состав: помидоры, сыр моцарелла и соус для пиццы, 
                               сдобрена оливковым маслом и сыром пармезан',
            'size' => '30 см',
            'price' => '78',
        ]);
        DB::table('pizzas')->insert([
            'name' => 'Венеция',
            'photo' => 'https://grill-wine.com/wp-content/uploads/2016/01/venecia.jpg',
            'description' =>  'Состав: соус для пиццы, салями, перец, маслины, сыр моцарелла',
            'size' => '30 см',
            'price' => '98',
        ]);

        DB::table('pizzas')->insert([
            'name' => 'Мясная',
            'photo' => 'https://grill-wine.com/wp-content/uploads/2016/01/miaso.jpg',
            'description' =>  'Состав: соус для пиццы, сыр моцарелла, охотничьи колбаски, бекон, ветчина, помидор, чесночный соус.',
            'size' => '30 см',
            'price' => '114',
        ]);

        DB::table('pizzas')->insert([
            'name' => '4 сыра',
            'photo' => 'https://grill-wine.com/wp-content/uploads/2016/01/4sira.jpg',
            'description' =>  'Состав: Соус для пиццы, сыр моцарелла, сыр гауда, сыр пармезан, сыр дор-блю',
            'size' => '30 см',
            'price' => '116',
        ]);
         
    }
}
