<?php

namespace App\Conversations;

use App\User;

trait BaseConversation
{
    public function getChatId()
    {
        return $this->bot->getUser()->getId();
    }

    public function getUser()
    {
        return User::where('chat_id', $this->getChatId())->first();  
    } 
}
