<?php

namespace App\Conversations;

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

class ExampleConversation extends Conversation
{
    /**
     * First question
     */
    public function askReason()
    {
        $question = Question::create("Huh - you woke me up. What do you need?")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('Tell a joke')->value('joke'),
                Button::create('Give me a fancy quote')->value('quote'),
                Button::create('Give me a photo')->value('photo'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'joke') {
                    $joke = json_decode(file_get_contents('http://api.icndb.com/jokes/random'));
                    $this->say($joke->value->joke);
                } elseif($answer->getValue() === 'photo') {
                    $attachment = new Image('https://az158878.vo.msecnd.net/marketing/Partner_21474845903/Product_42949679056/Asset_3cf80800-2bcc-46c5-88bd-ab8788b914d1/LogoFinal.png', [
                        'custom_payload' => true,
                    ]);
                    
                    // Build message object
                    $message = OutgoingMessage::create('This is my text')
                                ->withAttachment($attachment);
                    
                    // Reply message object
                    $this->say($message);
                     
                    
                    // Reply message object
                    //$this->say($message);
                } else {
                     
                    $this->say(Inspiring::quote());
                }
            }
        });
    }

    

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askReason();
    }
}
