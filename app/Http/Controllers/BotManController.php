<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;   

use Log;
use App\Models\File as Incoming;
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

    public function handle(Request $request)
    {

        $config = [
            'telegram' => [
                'token' => config('botman.telegram.token'),
            ]
        ];

        DriverManager::loadDriver(TelegramDriver::class);
        DriverManager::loadDriver(TelegramFileDriver::class);

        $botman = BotManFactory::create($config); //app('botman');


        /*$botman->hears('{any}', function ($bot,$any) {
            $bot->reply('Hello '.$any);
           // $bot->reply('✅');
            //$bot->reply('❌');
        });*/


        $botman->receivesFiles(function($bot, $files) use($request) {

            // grab data of user 
            $user = $bot->getUser();
            $username = $user->getUsername();
            $data = json_decode($bot->getMessage()->getPayload());
            $message_id = $data->message_id;
            $chat_id = $data->chat->id;

            //Log::debug(json_encode($data));
            


            foreach ($files as $file) {
        
                $url = $file->getUrl(); // The direct url
                //$payload = $file->getPayload(); // The original payload
            
                $virusTotal = new VirusTotal();
                $response = $virusTotal->scan($url);
                
                // grab data from virus total 
                $resource = $response['resource'];
                $link = $response['permalink'];
                $message = $response['verbose_msg'];
                

                // save new incoming message 
                $newmsg = new Incoming();
                $newmsg->username = $username;
                $newmsg->message_id = $message_id;
                $newmsg->chat_id = $chat_id;
                $newmsg->resource = $resource;
                $newmsg->link = $link;
                $newmsg->save();

                //Log::debug($chat_id);
                //Log::debug($message_id);
                //Log::debug($resource);
                //Log::debug($link);
                //Log::debug($message);
                //Log::debug($username);
                //Log::debug($request->ip());

                $botresponse = "[".$message."](".$link.")";


                // reply some response to user         
                $bot->sendRequest('sendMessage', [
                    'parse_mode' => 'markdown' , 
                    'chat_id' => $chat_id  , 
                    'reply_to_message_id' => $message_id , 
                    'text' => $botresponse , 
                ]);
                
            }

        });


        $botman->listen();

    }

}
