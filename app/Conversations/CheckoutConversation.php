<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use App\Info;
use App\Pizza;
use App\User;
use App\Order;

class CheckoutConversation extends Conversation
{
    use BaseConversation;
    
    public function startCheckuot()
    {
        $this->say($this->orderSummary());

        $question = Question::create('Оформим заказ?')
        ->callbackId('startCheckuot')
        ->addButtons([
            Button::create('Добавить еще пиццу')->value('add_pizza'),
            Button::create('Оформить заказ')->value('checkout'),
            Button::create('Отбой, таки придется есть лапшу')->value('refuse'),
        ]);

    $this->ask($question, function (Answer $answer) {
        if ($answer->isInteractiveMessageReply()) {

            $user = $this->getUser();
            
            $order = $user->order;

            if ($answer->getValue() === 'add_pizza') {

                $this->bot->startConversation(new OrderConversation());

            } elseif ($answer->getValue() === 'checkout') {
                if ($user->phone && $user->address) {
                    $this->bot->startConversation(new ConfirmationConversation());
                } else {
                    $this->bot->startConversation(new DataCollectionConversation());
                }

            } else {
                 $this->say(Info::find(1)->notice);
                 $order->delete();
            }
             
        }
    });
    }

    public function orderSummary()
    {
        $pizzas = Pizza::all();
        $chatId = $this->bot->getUser()->getId();
        $user = User::where('chat_id', $chatId)->first();
        $order = $user->order;

        $result = '';
        foreach ($pizzas as $pizza) {
            $countPizza = substr_count($order->pizzas, $pizza->name);
            if ($countPizza > 0) {
                $result .= $pizza->name . ' - ' . $countPizza . PHP_EOL;
            }
        }
        $summary = 'Ваш заказ' . PHP_EOL . '-----------------' . PHP_EOL . $result 
                   . 'На сумму - ' . $order->amount . ' грн';  
        return $summary;

    }
    
    public function run()
    {
        $this->startCheckuot();
    }
}
