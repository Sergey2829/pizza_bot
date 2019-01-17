<?php

use Illuminate\Database\Seeder;

class InfosTableSeeder extends Seeder
{
     
    public function run()
    {
        DB::table('infos')->insert([
            'notice' => 'Всего доброго! Обращайтесь. В любое время готов принять ваш заказ. Просто напишите любое слово!',
        ]);
        DB::table('infos')->insert([
            'notice' => 'Укажите номер Вашего телефона (актуальный!). Без обратного звонка мы не сможем отправить Ваш заказ',
        ]);
    }
}
