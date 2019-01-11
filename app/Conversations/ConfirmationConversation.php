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

class ConfirmationConversation extends Conversation
{
    public function confirmation()
    {
        $summary = $this->orderSummary();
        $chatId = $this->bot->getUser()->getId();
        $user = User::where('chat_id', $chatId)->first();
        $order = Order::where('user_id', $user->id)->first();
        $orderData = $summary . PHP_EOL . '-----------------' . PHP_EOL 
                    . 'Ваш телефон - ' . $user->phone . PHP_EOL 
                    . 'Ваш адрес - ' . $user->address;

        $this->say($orderData); 
        
        $question = Question::create('Подтверждаете заказ?')
                    ->callbackId('confirm_order')
                    ->addButtons([
                        Button::create('Да! Привезите поскорее!')->value('confirmation'),
                        Button::create('Добавить еще пиццу')->value('add_pizza'),
                        Button::create('Изменить контактные данные')->value('change_data'),
                        Button::create('Отменить! Мы лапше не изменяем!')->value('refuse_4'),
                    ]);
        
         
        $this->ask($question, function (Answer $answer) use ($order, $orderData) {
          if ($answer->isInteractiveMessageReply()) {
            
            if ($answer->getValue() === 'confirmation') {
                $message = wordwrap($orderData, 70 , "\r\n");
                mail('sergey2829@gmail.com', 'Заказ от Чатбота', 'test');
                $this->say('Ваш заказ принят. Мы скоро позвоним для уточнения деталей.');
                $contents = Storage::get('info.txt');
                $this->say($contents);
                $order->delete();

            } elseif ($answer->getValue() === 'add_pizza') {

                $this->bot->startConversation(new OrderConversation());

            } elseif ($answer->getValue() === 'change_data') {

                $this->bot->startConversation(new DataCollectionConversation());

            } else {

                $contents = Storage::get('info.txt');
                $this->say($contents);
                $order->delete();
            }
        }
      }); 
    }

    protected function orderSummary()
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
        $this->confirmation();
    }
}
