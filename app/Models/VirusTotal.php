<?php

namespace App\Models;

use Log;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirusTotal extends Model
{
    use HasFactory;

    private  $url = "https://www.virustotal.com/vtapi/v2/file";

    private function getApiKey()
    {
        return config('virustotal.config.key');
    }

    public function scan($file)
    {
        $data = array('apikey' => $this->getApiKey(),'file'=> $file);
        return $this->send('/scan',$data);
    }

    public function report($resource)
    {
        $data = array('apikey' => $this->getApiKey(),'resource'=> $resource);
        return $this->send('/report',$data);

    }
  

    public function send($target,$data = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url.$target);
        curl_setopt($ch, CURLOPT_POST, True);
        //curl_setopt($ch, CURLOPT_VERBOSE, 1); // remove this if your not debugging
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate'); // please compress data
        curl_setopt($ch, CURLOPT_USERAGENT, "gzip, My php curl client");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER ,True);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    
        $result=curl_exec ($ch);
        Log::debug(json_encode($result));

        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($status_code == 200) { // OK
            $js = json_decode($result, true);
            curl_close ($ch);
            return $js;
        } else {  // Error occured
            Log::error($result);
            curl_close ($ch);
            return $result;
        }
    }
}
