<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use App\Pizza;
use App\User;

class DataCollectionConversation extends Conversation
{
     
    public function askTel()
    {
        $this->ask('Укажите номер Вашего телефона (актуальный!). Без обратного звонка мы не сможем отправить Ваш заказ',
                    function (Answer $answer) {
                         $chatId = $this->bot->getUser()->getId();
                         $user = User::where('chat_id', $chatId)->first();
                          $user->phone = $answer->getText();
                          $user->save();
                          $this->bot->typesAndWaits(1);
                          $this->askAddress();
                    });

        
    }

    public function askAddress() 
    {
        $this->ask('Спасибо! Теперь укажите Ваш адрес', function (Answer $answer) {
              $chatId = $this->bot->getUser()->getId();
              $user = User::where('chat_id', $chatId)->first();
              $user->address = $answer->getText();
              $user->save();

              $this->bot->startConversation(new ConfirmationConversation());
        });

        
    }

    public function run()
    {
        $this->askTel();
    }
}
