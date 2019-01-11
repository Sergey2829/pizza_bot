<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use Storage;
use App\Pizza;
use App\User;
use App\Order;

class CheckoutConversation extends Conversation
{
    
    public function startCheckuot()
    {
        $this->say($this->orderSummary());

        $question = Question::create('Оформим заказ?')
        ->fallback('Что-то пошло не так, попробуйте еще раз')
        ->callbackId('startCheckuot')
        ->addButtons([
            Button::create('Добавить еще пиццу')->value('add_pizza'),
            Button::create('Оформить заказ')->value('checkout'),
            Button::create('Отбой, таки придется есть лапшу')->value('refuse_3'),
        ]);

    $this->ask($question, function (Answer $answer) {
        if ($answer->isInteractiveMessageReply()) {
            
            $chatId = $this->bot->getUser()->getId();
            $user = User::where('chat_id', $chatId)->first();
            $order = Order::where('user_id', $user->id)->first();

            if ($answer->getValue() === 'add_pizza') {

                $this->bot->startConversation(new OrderConversation());

            } elseif ($answer->getValue() === 'checkout') {
                if ($user->phone && $user->address) {
                    $this->bot->startConversation(new ConfirmationConversation());
                } else {
                    $this->bot->startConversation(new DataCollectionConversation());
                }

            } else {
                 $contents = Storage::get('info.txt');
                 $this->say($contents);
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
        $order = Order::where('user_id', $user->id)->first();

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
