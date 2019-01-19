<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use App\Pizza;
use App\User;
use App\Info;

class DataCollectionConversation extends Conversation
{
    use BaseConversation;
     
    public function askTel()
    {
        $this->ask(Info::find(2)->notice,
                    function (Answer $answer) {
                        $user = $this->getUser();
                        $user->phone = $answer->getText();
                        $user->save();
                        $this->askAddress();
                    }); 
    }

    public function askAddress() 
    {
        $this->ask('Спасибо! Теперь укажите Ваш адрес', function (Answer $answer) {
              
              $user = $this->getUser();
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
