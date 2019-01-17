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
use Mail;

class ConfirmationConversation extends Conversation
{
    use BaseConversation;

    public function confirmation()
    {
        $summary = $this->orderSummary();
        $user = $this->getUser();
        $order = $user->order;
        $orderData = $summary . PHP_EOL . '--------------------' . PHP_EOL 
                    . 'Ваш телефон - ' . $user->phone . PHP_EOL 
                    . 'Ваш адрес - ' . $user->address;
        
        $orderDataToAdmin = trim($summary, 'Ваш') . PHP_EOL . 'Телефон - ' . $user->phone 
                            . PHP_EOL . 'Адрес - ' . $user->address;

        $this->say($orderData); 
        
        $question = Question::create('Подтверждаете заказ?')
                    ->callbackId('confirm_order')
                    ->addButtons([
                        Button::create('Да! Привезите поскорее!')->value('confirmation'),
                        Button::create('Добавить еще пиццу')->value('add_pizza'),
                        Button::create('У меня другой телефон и/или адрес')->value('change_data'),
                        Button::create('Отменить! Мы лапше не изменяем!')->value('refuse'),
                    ]);
        
         
        $this->ask($question, function (Answer $answer) use ($order, $orderData, $orderDataToAdmin) {
          if ($answer->isInteractiveMessageReply()) {
            
            if ($answer->getValue() === 'confirmation') {
                $message = wordwrap($orderData, 70 , "\r\n");
                $this->say('Ваш заказ принят. Мы скоро позвоним для уточнения деталей.');
                $this->say(Info::find(1)->notice);
   
                Mail::raw($orderDataToAdmin, function ($message) {
                    
                    $message->from('9ec955174e-bd5a6a@inbox.mailtrap.io', 'OrderPizzaBot');
                  
                    $message->to('9ec955174e-bd5a6a@inbox.mailtrap.io')->subject('Новый заказ!');
                  });
                $order->delete();

            } elseif ($answer->getValue() === 'add_pizza') {

                $this->bot->startConversation(new OrderConversation());

            } elseif ($answer->getValue() === 'change_data') {

                $this->bot->startConversation(new DataCollectionConversation());

            } else {
                $this->say(Info::find(1)->notice);
                $order->delete();
            }
        }
      }); 
    }

    protected function orderSummary()
    {
        $pizzas = Pizza::all();
        $user = $this->getUser();
        $order = $user->order;

        $result = '';
        $pizzsStr = $order->pizzas;
        foreach ($pizzas as $pizza) {
            $countPizza = substr_count($pizzsStr, $pizza->name);
            if ($countPizza > 0) {
                $result .= $pizza->name . ' - ' . $countPizza . PHP_EOL;
            }
        }
        $summary = 'Ваш заказ' . PHP_EOL . '---------------------' . PHP_EOL . $result 
                   . 'На сумму - ' . $order->amount . ' грн';  
        return $summary;

    }
    
    public function run()
    {
        $this->confirmation();
    }
}
