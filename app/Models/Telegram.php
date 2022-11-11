<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Telegram extends Model
{
    use HasFactory;


    private $url = "https://api.telegram.org/bot";
    private $chatID = "";
    private $messageID = "";
    
    function __construct($chatID,$messageID){
        $this->chatID = $chatID;
        $this->messageID = $messageID;
    }

    private function GetbotToken()
    {
        return config('botman.telegram.token');
    }
    
    function send_message($message){
    
    $data = array(
            "parse_mode" => "markdown" , 
            "chat_id" => $this->chatID ,
            "reply_to_message_id"  => $this->messageID , 
            "text" => $message , 
        );
    
      $website=$this->url.$this->GetbotToken();
      $ch = curl_init($website . '/sendMessage?'.http_build_query($data));
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_FAILONERROR, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      $result = curl_exec($ch);
      if (curl_errno($ch)) {
            $result = curl_error($ch);
            error_log(print_r($result,true));
      }
      curl_close($ch);
      
      return $result;
    }
}
