<?php
use App\Http\Controllers\BotManController;

$botman = resolve('botman');

$botman->hears('([/\w\sа-я]+)', BotManController::class.'@startConversation');