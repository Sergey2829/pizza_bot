<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use App\Info;
use App\Pizza;


class SelectMenuConversation extends Conversation
{
    
    
    public function showMenu()
    { 
        $pizzas = Pizza::all();
        foreach ($pizzas as $pizza) {
               $image = $pizza->photo;
               $text = 'Пицца ' . $pizza->name . ' - ' . $pizza->size . ' - ' . $pizza->price . ' грн'
               . PHP_EOL . $pizza->description;
               $name = $pizza->name;
               
            $this->addMenuItem($image, $text, $name); 
        }     
        
        $question = Question::create('Приготовить для вас эту вкусную пиццу?')
                    ->callbackId('is_start_order')
                    ->addButtons([
                        Button::create('Да, буду заказывать')->value('start_order'),
                        Button::create('Сегодня только лапша!')->value('refuse'),  
                    ]);

        $this->ask($question, function (Answer $answer) {
              if ($answer->isInteractiveMessageReply()) {
                       if ($answer->getValue() === 'start_order') {
                        $this->bot->startConversation(new OrderConversation());
                       } else {
                        $this->say(Info::find(1)->notice); 
                       }
                   }
        });
             
    }

    protected function addMenuItem($image, $text, $name)
    {

        $attachment = new Image($image);  
        $message = OutgoingMessage::create($text)
                    ->withAttachment($attachment);
        
        $this->say($message);
    }

    public function run()
    {
        $this->showMenu();
    }
}
