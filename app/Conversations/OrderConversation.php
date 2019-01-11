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
        ->addButtons([
            $this->addButtons($pizzas[0]->name),
            $this->addButtons($pizzas[1]->name),
            $this->addButtons($pizzas[2]->name),
            $this->addButtons($pizzas[3]->name),
        ]);
        
         
        $this->ask($question, function (Answer $answer) use ($pizzas) {
          if ($answer->isInteractiveMessageReply()) {
            
            if ($answer->getValue() === $pizzas[0]->name) {
                
                $this->addItem(0);
            } elseif ($answer->getValue() === $pizzas[1]->name) {
                $this->addItem(1); 
            }elseif ($answer->getValue() === $pizzas[2]->name) {
                $this->addItem(2); 
            } else {
                $this->addItem(3);
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

    protected function addButtons($pizzaName)
    {
        return Button::create($pizzaName)->value($pizzaName);
    }
    
    public function run()
    {
        $this->addPizzas();
    }
}
