<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;   

use Log;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Attachments\File;

//use BotMan\BotMan\BotManFactory;
//use BotMan\BotMan\Drivers\DriverManager;
//use BotMan\Drivers\Telegram\TelegramDriver;
//use BotMan\Drivers\Web\WebDriver;

class BotManController extends Controller
{

    public function handle()
    {
        $botman = app('botman');
        $botman->receivesFiles(function($bot, $files) {

            foreach ($files as $file) {
        
                $url = $file->getUrl(); // The direct url
                $payload = $file->getPayload(); // The original payload

                Log::debug($url);
                Log::debug($payload);
                
            }

            $bot->reply("Tell me more!");
        });

        $botman->listen();

    }


    /*
    public function handle()

    {
        $botman = app('botman');
   
        $botman->hears('{message}', function($botman, $message) {
   
            if ($message == 'hi') {
                $this->askName($botman);
            }
            
            else{
                $botman->reply("write 'hi' for testing...");
            }
   
        });
   
        $botman->listen();
    }*/
   
    /*public function askName($botman)
    {
        $botman->ask('Hello! What is your Name?', function(Answer $answer) {
   
            $name = $answer->getText();
   
            $this->say('Nice to meet you '.$name);
        });
    }*/
}
