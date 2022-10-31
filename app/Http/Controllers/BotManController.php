<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;   

use Log;
use App\Models\VirusTotal;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Attachments\File;

use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Telegram\TelegramDriver;
use BotMan\Drivers\Telegram\TelegramFileDriver;
//use BotMan\Drivers\Web\WebDriver;

class BotManController extends Controller
{

    public function handle()
    {

        $config = [
            'telegram' => [
                'token' => config('botman.telegram.token'),
            ]
        ];

        DriverManager::loadDriver(TelegramDriver::class);
        DriverManager::loadDriver(TelegramFileDriver::class);

        $botman = BotManFactory::create($config); //app('botman');


        $botman->receivesFiles(function($bot, $files) {

            // grab data of user 
            $user = $bot->getUser();
            $chat_id = $user->getId();
            $username = $user->getUsername();
            $data = json_decode($bot->getMessage()->getPayload());
            $message_id = $data->message_id;
            


            foreach ($files as $file) {
        
                $url = $file->getUrl(); // The direct url
                //$payload = $file->getPayload(); // The original payload
            
                $virusTotal = new VirusTotal();
                $response = $virusTotal->scan($url);
                
                // grab data from virus total 
                $resource = $response['resource'];
                $link = $response['permalink'];
                $message = $response['verbose_msg'];
                
                Log::debug($chat_id);
                Log::debug($message_id);
                Log::debug($resource);
                Log::debug($link);
                Log::debug($message);
                Log::debug($username);
                Log::debug(json_encode(request()->ip()));
                Log::debug(json_encode($data));


                // reply some response to user 
                $msg = $link."\r\n ";
                $msg.= $message;
                $bot->reply($msg);
                
            }

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
