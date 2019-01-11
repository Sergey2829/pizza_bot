<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use Storage;


class InitiatoryConversation extends Conversation
{
     
    
    public function askAim()
    {
        $question = Question::create('Здравствуйте! Желаете заказать пиццу?')
            ->callbackId('select_aim')
            ->addButtons([
                Button::create('Посмотреть меню')->value('menu'),
                Button::create('Не в этот раз. Решили варить лапшу')->value('refuse'),
            ]);

        $this->ask($question, function (Answer $answer) {
             if ($answer->isInteractiveMessageReply()) {
                 if ($answer->getValue() === 'menu') {
                      $this->bot->typesAndWaits(2);
                      $this->bot->startConversation(new SelectMenuConversation());
                 } else {
                      $contents = Storage::get('info.txt');
                      $this->say($contents);
                       
                 }  
             }
        });    
    }

    public function run()
    {
        $this->askAim();
    }
}
