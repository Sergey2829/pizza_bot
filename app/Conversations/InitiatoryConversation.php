<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use App\Info;

class InitiatoryConversation extends Conversation
{
     
    
    public function askAim()
    {
        
        $question = Question::create('Здравствуйте! Желаете заказать пиццу?')
            ->callbackId('select_aim')
            ->addButtons([
                Button::create('Посмотреть меню')->value('menu'),
                Button::create('Сделать заказ')->value('order'),
                Button::create('Не в этот раз. Решили варить лапшу')->value('refuse'),
            ]);

        $this->ask($question, function (Answer $answer) {
             if ($answer->isInteractiveMessageReply()) {
                   if ($answer->getValue() === 'menu') {
                      $this->bot->typesAndWaits(2);
                      $this->bot->startConversation(new SelectMenuConversation());
                 } elseif ($answer->getValue() === 'order') {
                    $this->bot->startConversation(new OrderConversation());
                 } else {
                      $this->say(Info::find(1)->notice);
                 }  
             }
        });    
    }

    public function run()
    {
        $this->askAim();
    }
}
