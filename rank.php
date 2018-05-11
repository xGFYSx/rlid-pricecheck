<?php

class Rank
{
    protected $apiKey = 'Z9VO8OQFV80MKCMBV7VJ23V3WTRO2UYU';

    public $currentSeason = '7';
    public $tierList = array("Unranked", "Bronze I", "Bronze II", "Bronze III", "Silver I", "Silver II", "Silver III", "Gold I", "Gold II", "Gold III", "Platinum I", "Platinum II", "Platinum III", "Diamond I", "Diamond II", "Diamond III", "Champion I", "Champion II", "Champion III", "Grand Champion");

    public $error = false;
    public $error_msg = '';
    public $error_code;
    
    public $query = '';
    public $platform = '';
    
    public $default_platform;
    
    //response from api
    public $response;
    
    //response Speech
    public $speech;
    
    //response displayText
    public $displayText;
    
    //set default platform
    public function __construct()
    {
        
        $this->default_platform = array(
            'pc' => array(
                'pc',
                'steam'
            ),
            'ps4' => array(
                'ps4',
                'ps'
            )
        );
    }

    function setQuery($query)
    {
        if( is_int( strpos($query,'!rank') ) == FALSE ){
          exit();
        }

        if (strlen($query) <= 6 ) {
            $this->error_code = 1;
            return $this;
        } else {
                //remove !price from string
            $query = str_replace('!rank ', '', $query);
            
                //remove platform from string
            foreach ($this->default_platform as $var) {
                foreach ($var as $varr) {
                    if( preg_match("/\b".$varr."\b/", $query) ){
                        $query = str_replace($varr, '', $query);
                        $this->platform = $var;
                    }   
                }
            }

            if (is_null($query)){
                $this->error_code = 0;
            }
            
            $this->query = $query;
            return $this;
        }
    }
 
    function setPlatform($platform)
    {
        switch ($platform):
            
            case '':
                $this->error_code = 1;
                break;
            
            case 'pc':
                $this->platform = '1';
                break;
            
            case 'ps4':
                $this->platform = '2';
                break;
            
            case 'ps':
                $this->platform = '2';
                break;
            
            default:
                $this->error_code = 2;
                break;
                
        endswitch;
        
        return $this;
    }
    
    function error($code, $external_msg = '')
    {
        switch ($code):
            
            case 0:
                $msg = "\xE2\x9A\xA0 Usernamenya diisi dulu gan.";
                break;
            
            case 1:
                $msg = "\xE2\x9A\xA0 Platformnya diisi dulu gan (PC/PS4).";
                break;

            case 2:
                $msg = "\xE2\x9A\xA0 Platform yang tersedia hanya PC dan PS4.";
                break;
            
            case 3:
                $msg = "\xE2\x9A\xA0 Usernamenya gak ketemu gan.";
                break;
            
            default:
                //return the text
                $msg = $code;
                break;
                
        endswitch;
        
        $this->error     = true;
        $this->error_msg = $msg;
        
        $this->speech = $msg;
        return $msg;
    }
    
    function result($msg)
    {
        $this->response = $msg;
        return $msg;
    }

    function getMMR($playlistID, $playlist)
    {
        $mmr = '';
        $div = '';
        $point = '';

        $tierID = $playlist->tier;
        $tier = $this->tierList[$tierID];
        if ($tierID != 0){
            $point .= $playlist->rankPoints." -";
            $div .= "Div ".($playlist->division+1);
        }

        switch ($playlistID) {
            case '10':
            $mmr .= "\t\t\xE2\x96\xAA Duel (1v1): $point $tier $div\n";
                break;
            
            case '11':
            $mmr .= "\t\t\xE2\x96\xAA Doubles (2v2): $point $tier $div\n";
                break;

            case '12':
            $mmr .= "\t\t\xE2\x96\xAA Solo Standard (3v3): $point $tier $div\n";
                break;

            case '13':
            $mmr .= "\t\t\xE2\x96\xAA Standard (3v3): $point $tier $div\n";
                break;
        }
        return $mmr;
        
    }

    function getRank()
    {
        $key = $this->apiKey;
        $platform = $this->platform;
        $user = $this->query;

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.rocketleaguestats.com/v1/player?platform_id=$platform&unique_id=$user",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "authorization: $key",
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $temp =  json_decode($response);

        if(isset($temp->code) && ($temp->code == "404")){
            $this->error_code = 3;
        }

        $this->response = $temp;

        if ($err) {
            $this->error($err);
        } else {
            // return $response;
            return $this->_makeResponse($response);
        }
    }

    function _makeResponse()
    {
        $i = $this->currentSeason;
        $response = $this->response;
        $error_code = $this->error_code;

        //check error
        if (isset($error_code)) {
            return $this->error($error_code);
        }

        // Generate text
        $result = '';
        $player = htmlspecialchars_decode($response->displayName);
        $result .= "\xF0\x9F\x98\xB6 Nama Player: $player\n";
        $result .= "\xF0\x9F\x8E\xAE Platform: ".$response->platform->name."\n";
        $result .= "\xF0\x9F\x93\x8A Ranked MMR:\n";
        foreach ($response->rankedSeasons->{$i} as $playlistID => $playlist) {
            $result .= $this->getMMR($playlistID, $playlist);
        }
        $result .= "\xF0\x9F\x8C\x90 $response->profileUrl";
        return $result;
    }
}

?>
