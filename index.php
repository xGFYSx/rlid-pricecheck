<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$method = $_SERVER['REQUEST_METHOD'];

//include file
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'response.php';
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'item.php';
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'rank.php';

$response = new ResponseMsg;


// Process only when method is POST
if ($method) {
    $requestBody = file_get_contents('php://input');
    $json        = json_decode($requestBody);
    
    if (isset($_GET['debug'])) {
        if ($_GET['debug'] == 'php'):
            echo '<pre>' . print_r($json->result, TRUE) . '</pre>';
        else:
            echo json_encode($json);
        endif;
    }
    
    //check the text
    if (isset($json->result->resolvedQuery)) {
        $text = $json->result->resolvedQuery;
    }
    
    //check action
    if (isset($json->result->action)) {
        $action = $json->result->action;
    }
    
    //check action, future implementation
    if (isset($action)) {
        
        switch ($action):
            
            case 'checkprice':
                $item = new Item;
                try {
                    $result = $item->setQuery($json->result->resolvedQuery)->setPlatform()->getPrice()->makeResponse();
                    echo $response->setText($result, $result)->result();
                    die();
                }
                catch (Exception $e) {
                    echo var_dump($e->getCode());
                    echo var_dump($e->getMessage());
                    die();
                    $result = $rank->error($e->getCode(), $e->getMessage();
                    echo $response->setText($result, $result)->result();
                    die();
                }
                break;
            
            case 'checkrank':
                $rank = new Rank;
                try {
                    $result = $rank->setQuery($json->result->resolvedQuery)->setPlatform($rank->platform)->getRank()->makeResponse();
                    echo $response->setText($result, $result)->result();
                    die();
                }
                catch (Exception $e) {
                    $result = $rank->error($e->getMessage());
                    echo $response->setText($result, $result)->result();
                    die();
                }
                break;
        endswitch;
    }
    
    //normal response
    switch ($text) {
        case '!credit':
            $text = "All prices courtesy of https://rl.insider.gg\n Rocket League API provided by https://rocketleaguestats.com\n Special thanks to:\n \xE2\x96\xAA Ruffe - Ananta Rizki F. (Mastermind) \n \xE2\x96\xAA xGFYSx - Dewantara Tirta (Programmer)\n \xE2\x96\xAA FRDS  - Agung Firdaus (Tester and Helper)\n \xE2\x96\xAA Yggdrasil128 and colleagues (Devs at RL Insider)\n \xE2\x96\xAA AeonLucid (API dev at RLStats).";
            break;
        
        case '!help':
            $text = "\xE2\x9D\x97 Daftar perintah :\n \xE2\x96\xAA !help - Menampilkan pesan panduan\n \xE2\x96\xAA !price <nama item> <warna (optional)> <platform> - Mengecek harga item\n \xE2\x96\xAA !rank <platform (PC/PS4)> <Username> - Mengecek rank/MMR kamu\n \xE2\x96\xAA !mabar - Menampilkan pesan otomatis mabar/sparring RLID\n \xE2\x96\xAA !rlid - Menampilkan daftar media sosial RLID\n \xE2\x96\xAA !credit - Menampilkan pesan credit";
            break;
        
        case '!rlid':
            $text = "\xE2\x9E\xA1 Facebook Page : http://www.facebook.com/RocketLeagueID\n\xE2\x9E\xA1 Grup Steam : http://steamcommunity.com/groups/RLID\n\xE2\x9E\xA1 Instagram : http://www.instagram.com/rocketleague.id\n\xE2\x9E\xA1 Grup Line Square : http://line.me/ti/g2/ICUFW8K9FE\n\xE2\x9E\xA1 Discord : https://discord.gg/Fg7B557";
            break;
        
        case '!mabar':
            $text = "\xF0\x9F\x8F\x81 SPARRING/MABAR RLID \xE2\x9A\xBD \xF0\x9F\x8F\x8E\n\xF4\x80\x82\x8D Ayo ikut sparring/mabar komunitas!\n\nIkutnya gampang, tinggal join private match dengan format room :\n\xE2\x9E\xA1 name : rlid\n\xE2\x9E\xA1 password : rlid\n\n\xE2\x9D\x97 Jangan lupa untuk join di voice chat discord di channel \"Parkiran\"";
            break;
        
        //no response (biar gak duplikat, atau ngulang kalimat kita)
        default:
            die();
            break;
    }
    echo $response->setText($text, $text)->result();
    die();
} else {
    echo 'Not allowed';
}

?>
