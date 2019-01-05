<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use App\Pizza;

class SelectMenuConversation extends Conversation
{
    
    public function selectMenu()
    {
        $pizzas = Pizza::all();

        foreach ($pizzas as $pizza) {
               $image = $pizza->photo;
               $text = 'Пицца ' . $pizza->name . ' - ' . $pizza->size . ' - ' . $pizza->price
               . "<br />" . $pizza->description;
               $name = $pizza->name;
               
               $this->addMenuItem($image, $text, $name); 
        }

        
        
    }

    protected function addMenuItem($image, $text, $name)
    {
        $attachment = new Image($image, [
            'custom_payload' => true,
        ]);
        
        $message = OutgoingMessage::create($text)
                    ->withAttachment($attachment);

        $question = Question::create('')
            ->callbackId('select_pizza')
            ->addButtons([
                Button::create('Заказать пиццу ' . $name)->value($name),
            ]);
        
        $this->say($message);
         
        $this->ask($question, function (Answer $answer) {
                if ($answer->isInteractiveMessageReply()) {
                     
                }
           });  
    }

    public function run()
    {
        $this->selectMenu();
    }
}
