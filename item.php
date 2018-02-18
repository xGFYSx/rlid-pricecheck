<?php

 class Item {

 	protected $apikey = 'GxpvmYEDSXPkkQizT0kvnfGiQyyQsBjK';
  protected  $apiUrl = 'https://rl.insider.gg/api/pricebotExternal';


  public $error = false;
  public $error_code = '';

  public $item_name = '';
  public $platform = 'pc';

  //response from api
  public $response = false;

  //response Speech
  public $speech;

  //response displayText
  public $displayText;


  /*
   * [setItem description]
   * @param [array] $paint [description]
   * @param [array] $item  [description]
   */
  function setItem($paint, $item)
  {
      $item_name='';

      if( count($paint) > 0 ){
        foreach($paint as $key => $val){
          $item_name .= $val . ' ';
        }
      }

      //cek item kosong
      if(count($item)==0){
        $this->error(0);
      }
      //item yang digunakan adl item pertama (jika terdapat byk item)
      $item_name = $item[0];

      $this->item_name = $item_name;
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
        'item' => $this->item_name
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

        //define paint
        $paint = '';
        if( isset($response->PaintName) && $response->PaintName != 'Default' ){
          $paint = "$response->PaintName";
        }

        //define cert
        $cert = '';
        if( isset($response->Cert) && $response->Cert != FALSE ){
          $cert = "$response->Cert";
        }

        //generate Speech
        $this->speech = "The price for $paint $cert $response->ItemName is $response->Price key";

        $result ='';

        $result .= $paint."$response->ItemName \n";

        if( isset($response->Cert) && $response->Cert != FALSE ){
          $result .= "Cert : $response->Cert \n";
        }
        $result .= "Price : $response->Price ";
        // $result .= "$response->URL";

        $this->displayText = $result;
        return $result;
      }

    }

 }

?>
