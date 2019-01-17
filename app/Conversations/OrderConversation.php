<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use App\Pizza;
use App\User;
use App\Order;

class OrderConversation extends Conversation
{
    public function addPizzas()
    {
        $pizzas = Pizza::all();

        $question = Question::create('Какую пиццу желаете?')
        ->callbackId('select_pizza')
        ->addButtons($this->addButtons());
         
        $this->ask($question, function (Answer $answer) use ($pizzas) {
          if ($answer->isInteractiveMessageReply()) {

            for ($i = 0; $i <= 3; ++$i) {
                if ($answer->getValue() === $pizzas[$i]->name) {
                    $this->addItem($i);
                }
            }
        }
      });
    }

    protected function addItem($num)
    {
        $user = User::firstOrCreate([
           'chat_id'=>$this->bot->getUser()->getId()]);
       
        $order = Order::firstOrCreate([
           'user_id'=>$user->id,
        ]);

        $currentOrder = $order->pizzas;
        $currentAmount = $order->amount;
        $pizzas = Pizza::all();

        $order->pizzas = $currentOrder. ' ' . $pizzas[$num]->name;
        $order->amount = $currentAmount + $pizzas[$num]->price;
        $order->save();
       
       $this->bot->startConversation(new CheckoutConversation());
    }

    protected function addButtons()
    {
        $pizzas = Pizza::all();
        $buttons = [];
        foreach ($pizzas as $pizza) {
            $buttons[] = Button::create($pizza->name)->value($pizza->name);
        }
        return $buttons;
    }
    
    public function run()
    {
        $this->addPizzas();
    }
}
