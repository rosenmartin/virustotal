<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\File as Incoming;
use App\Models\VirusTotal;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;

use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Telegram\TelegramDriver;
use BotMan\Drivers\Telegram\TelegramFileDriver;

class CheckFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'virustotal:checkfiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get resports from virus total and send reply messages in Bot';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $config = [
            'telegram' => [
                'token' => config('botman.telegram.token'),
            ]
        ];

        DriverManager::loadDriver(TelegramDriver::class);
        DriverManager::loadDriver(TelegramFileDriver::class);

        $botman = BotManFactory::create($config); 

        $msgs = Incoming::where('isSent','0')->orderBy('id','desc')->get();
        if(!$msgs->isEmpty()){
            foreach ($msgs as $msg) {

                $vt = new VirusTotal();
                $response = $vt->report($msg->resource);

                //dd($response);
    
                $botman->sendRequest('sendMessage', [
                        'chat_id' => $msg->chat_id  , 
                        'reply_to_message_id' => $msg->message_id , 
                        'text' => $response['permalink']."\r\n ".$response['verbose_msg'] , 
                ]);
    
                $msg->isSent = 1 ; 
                $msg->save();
            }
        }
    }
}
