<?php

class Rank
{
    protected $apiKey = 'Z9VO8OQFV80MKCMBV7VJ23V3WTRO2UYU';
    
    public $currentSeason = '8';
    public $tierList = array("Unranked", "Bronze I", "Bronze II", "Bronze III", "Silver I", "Silver II", "Silver III", "Gold I", "Gold II", "Gold III", "Platinum I", "Platinum II", "Platinum III", "Diamond I", "Diamond II", "Diamond III", "Champion I", "Champion II", "Champion III", "Grand Champion");
    
    public $error = false;
    public $error_msg = "";
    
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
                "/\bpc\b/i",
                "/\bpc$/i",
                "/\bsteam\b/i",
                "/\bsteam$/i"
            ),
            'ps4' => array(
                "/\bps4\b/i",
                "/\bps4$/i",
                "/\bps\b/i",
                "/\bps$/i"
            )
        );
    }
    
    function setQuery($query)
    {
        if (is_int(strpos($query, '!rank')) == FALSE) {
            exit();
        }
        
        if (strlen($query) <= 6) {
            throw new Exception('1');
        } else {
            //remove !price from string
            $query = str_replace('!rank ', '', $query);
            
            //remove platform from string
            foreach ($this->default_platform as $key => $var) {
                foreach ($var as $varr) {
                    if (preg_match($varr, $query)) {
                        $query          = preg_replace(array(
                            $varr,
                            "/\s+/"
                        ), '', $query);
                        $this->platform = $key;
                    }
                }
            }
            
            $this->query = $query;
            return $this;
        }
    }
    
    function setPlatform($platform)
    {
        switch ($platform):
            
            case '':
                throw new Exception('1');
                break;
            
            case 'pc':
                $this->platform = '1';
                break;
            
            case 'ps4':
                $this->platform = '2';
                break;
            
            default:
                throw new Exception('2');
                break;
                
        endswitch;
        
        return $this;
    }
    
    function error($code)
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
                $msg = "\xF4\x80\x81\xBA Usernamenya gak ketemu gan. Mungkin salah ketik?";
                break;
            
            case 4:
                $msg = "\xF4\x80\x81\xBB Wado! Server databasenya down gan!";
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
        $mmr   = '';
        $div   = '';
        $point = '';
        
        $tierID = $playlist->tier;
        $tier   = $this->tierList[$tierID];
        if ($tierID != 0) {
            $point .= $playlist->rankPoints . " -";
            $div .= "Div " . ($playlist->division + 1);
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
        if ($this->query == '') {
            throw new Exception('0');
        } else {
            $key      = $this->apiKey;
            $platform = $this->platform;
            $user     = $this->query;
            
            $curl = curl_init();
            
            //set curl option
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.rocketleaguestats.com/v1/player?platform_id=$platform&unique_id=$user",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "authorization: $key"
                )
            ));
            
            $response = curl_exec($curl);
            $err      = curl_error($curl);
            curl_close($curl);
            $temp = json_decode($response);
            
            if (isset($temp->code)) {
                switch ($temp->code) {
                    
                    case '404':
                        throw new Exception('3');
                        break;
                    case '500':
                        throw new Exception('4');
                        break;
                    case '503':
                        throw new Exception('4');
                        break;
                }
            }
            
            $this->response = $temp;
            return $this;
        }
    }
    
    function makeResponse()
    {
        $i        = $this->currentSeason;
        $response = $this->response;
        
        // Generate text
        $result = "";
        $player = htmlspecialchars_decode($response->displayName);
        $result .= "\xF0\x9F\x98\xB6 Nama Player: $player\n";
        $result .= "\xF0\x9F\x8E\xAE Platform: " . $response->platform->name . "\n";
        $result .= "\xF0\x9F\x93\x8A Ranked MMR:\n";
        foreach ($response->rankedSeasons->{$i} as $playlistID => $playlist) {
            $result .= $this->getMMR($playlistID, $playlist);
        }
        $result .= "\xF0\x9F\x8C\x90 $response->profileUrl";
        return $result;
    }
}

?>
