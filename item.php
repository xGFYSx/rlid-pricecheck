<?php

 class Item {

 	protected $apikey = 'GxpvmYEDSXPkkQizT0kvnfGiQyyQsBjK';
  protected  $apiUrl = 'https://rl.insider.gg/api/pricebotExternal';


  public $error = false;
  public $error_code = '';

  public $query = '';
  public $platform = 'pc';

  public $default_platform;

  //response from api
  public $response = false;

  //response Speech
  public $speech;

  //response displayText
  public $displayText;


  //set default platform
  public function __construct(){

    $this->default_platform = array(
        'pc' => array( 'pc', 'steam' ),
        'ps4' => array( 'ps4', 'ps' )
      );

  }


  /*
   * [setQuery]
   * @param [array] $paint [description]
   */
  function setQuery($query)
  {
    //remove !price from string
    $query = str_replace('!price ', '', $query);

    //remove platform from string
    foreach( $this->default_platform as $var ) {
       foreach( $var as $varr ){
        $query = str_replace($varr, '', $query);
       }
    }

    $this->query = $query;
    return $this;
  }


  /**
   * setPlatform
   * @param string $platform [pc,ps4]
   */
  function setPlatform($platform='pc')
  {
      switch ($platform):

          case '':
            $this->platform = 'pc';
          break;

          case strtolower('pc'):
            $this->platform = 'pc';
          break;

          case strtolower('ps4'):
            $this->platform = 'ps4';
          break;

          case strtolower('ps'):
            $this->platform = 'ps4';
          break;

          default:
            $this->error(1);
          break;

      endswitch;

      return $this;
  }




  /**
   * error (define error message)
   * @param  [int] $code [description]
   * @return [type]       [description]
   */
  function error($code,$external_msg='')
  {
      switch($code):

        case 0:
            $msg = 'Itemnya diisi dulu gan';
        break;

        case 1:
            $msg = 'Platform yang tersedia hanya pc dan ps4';
        break;

        case 2:
            $msg = 'Item ini tidak ada di platform ' . strtoupper($this->platform);
        break;

        case 3:
            $msg = 'Tidak ada warna tersebut pada item ini ';
        break;

        case 4:
            $msg = 'Belum ada harga untuk item ini';
        break;

        case 5:
            $msg = "Duplicate Item! \n";
            $msg .= $external_msg;
        break;


        default:
            //return the text
            $msg = $code;
        break;

      endswitch;

      $this->error = true;
      $this->error_code = $msg;

      //
      $this->speech = $msg;
      if( (is_int($code) AND $code ==5) ){
        $this->speech = 'Duplicate Item';
      }
      $this->result($msg);
  }




  /**
   * [result description]
   * @param  [type] $msg [description]
   * @return [type]      [description]
   */
  function result($msg){
      $this->response = $msg;
      return $msg;
  }




  /**
   * [getPrice API]
   * https://rl.insider.gg/api/pricebotExternal
   * @param  [type] $item     [paint+item name] ex: "white zomba"
   * @param  [type] $platform [pc,ps4]
   * @return [str]           [displayText]
   */

 	function getPrice()
 	{
    $data = array(
        'platform' => $this->platform,
        'item' => $this->query
    );

    $curl = curl_init();

    //set curl option
    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->apiUrl,
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => array(
        "x-api-key: " . $this->apikey
      ),
      CURLOPT_RETURNTRANSFER => TRUE
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);
    $this->response = json_decode($response);

    if ($err) {
      $this->error($err);
    }
    else {
      // return $response;
      return $this->_makeResponse($response);
    }
 	}


  /**
   * makeResponse
   * @return [str]           [formatted str]
   */
  function _makeResponse()
  {
      $response = $this->response;

      //check error
      if( isset($response->ErrorCode) )
      {
        //jika multiple items
        if($response->ErrorCode == '4')
        {
          $err = '';
          foreach( $response->Matches as $val )
          {
            $err .= "$val \n";
          }
          $this->error(5,$err);
        }
        else //error yg lain
        {
            $this->error( $response->ErrorCode + 1 );
        }
      }
      else{

        $cert = FALSE;
        $color = FALSE;

        //check  cert
        if( isset($response->Cert) && $response->Cert != 'false' ){
          $cert = $response->Cert;
        }

        //check color
        if( isset($response->PaintName) && strtolower($response->PaintName) != 'default' ){
          $color = $response->PaintName;
        }

        //generate Speech
        $this->speech = "The price for ";

        //check cert
        if( $cert != FALSE ){
          $this->speech .= "$cert  ";
        }

        //check color
        if( $color != FALSE){
          $this->speech .= "$color ";
        }

        $this->speech .= "$response->ItemName is $response->Price keys";

        //generate displayText
        $result ='';

        if( $cert != FALSE ){
          $result .= "$cert  ";
        }

        if( $color != FALSE ){
          $result .= "$color ";
        }

        $result .= "\uDBC0 Nama Item : $response->ItemName \n";
        $result .= "Platform : ".strtoupper($this->platform)." \n";
        $result .= "Price : $response->Price \n";
        $result .= "$response->URL \n";

        return $result;
      }

    }

 }

?>
