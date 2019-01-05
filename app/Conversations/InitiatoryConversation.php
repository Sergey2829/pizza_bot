<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;


class InitiatoryConversation extends Conversation
{
     
    
    public function askAim()
    {
        $question = Question::create('Здравствуйте! Желаете заказать пиццу?')
            ->callbackId('select_aim')
            ->addButtons([
                Button::create('Посмотреть меню')->value('menu'),
                Button::create('Не в этот раз')->value('refuse'),
            ]);

             

        $this->ask($question, function (Answer $answer) {
             if ($answer->isInteractiveMessageReply()) {
                 if ($answer->getValue() === 'menu') {
                      $this->bot->startConversation(new SelectMenuConversation());
                 } else {
                      $this->say('Всего доброго! Обращайтесь. В любое время готов принять ваш заказ. 
                                  Просто напишите любое слово!');
                 }  
             }
        });    
    }


    public function run()
    {
        $this->askAim();
    }
}
