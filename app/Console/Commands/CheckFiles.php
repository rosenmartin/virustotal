<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\File as Incoming;
use App\Models\Telegram;
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
        $msgs = Incoming::where('isSent','0')->orderBy('id','desc')->get();

        if(!$msgs->isEmpty()){
            foreach ($msgs as $msg) {

                $vt = new VirusTotal();
                $response = $vt->report($msg->resource);

                //dd($response);
    
                if($response['response_code']==1&&isset($response['scan_date'])&&!empty($response['scan_date'])){
                

                    
                    $tl = new Telegram($msg->chat_id,$msg->message_id);
                    $botmessage = "[".$response['verbose_msg']."](". $response['permalink'].")";
                    $tl->send_message($botmessage);        

        
                    $msg->isSent = 1 ; 
                    $msg->save();
                }
            }
        }
    }
}
