<?php

namespace App\Models;

use Log;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirusTotal extends Model
{
    use HasFactory;

    private  $url = "https://www.virustotal.com/vtapi/v2/file";

    private $api_key = "d50287c0c14020774297c1188cfbd88165efca91134c7a2e63d64df6bc23ed9d";

    public function scan($file)
    {
        $data = array('apikey' => $this->api_key,'file'=> $file);
        return $this->send('/scan',$data);
    }

    public function report($resource)
    {
        $data = array('apikey' => $this->api_key,'resource'=> $resource);
        return $this->send('/report',$data);

    }
  

    public function send($target,$data = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url.'/scan');
        curl_setopt($ch, CURLOPT_POST, True);
        curl_setopt($ch, CURLOPT_VERBOSE, 1); // remove this if your not debugging
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate'); // please compress data
        curl_setopt($ch, CURLOPT_USERAGENT, "gzip, My php curl client");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER ,True);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    
        $result=curl_exec ($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        print("status = $status_code\n");
        if ($status_code == 200) { // OK
        $js = json_decode($result, true);
        return $js;
        } else {  // Error occured
            Log::error($result);
            return $result;
        }
        curl_close ($ch);

    }
}
