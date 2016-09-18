<?php

class Token{
  private $id;
  private $issued_at;
  private $expiration;
  private $enc_token;
  private $is_valid;
  private $diag;
  protected $auth_config;

  /**
   * @var Singleton The reference to *Singleton* instance of this class
   */
  private static $instance;

  /**
   * Returns the *Singleton* instance of this class.
   *
   * @return Singleton The *Singleton* instance.
   */
  public static function getInstance()
  {
      if (null === static::$instance) {
          static::$instance = new static();
      }

      return static::$instance;
  }

  protected function __construct(){

    $this->id = null;
    $this->issued_at = null;
    $this->expiration = null;
    $this->auth_config = include( __ROOT__."/backend/api/application/config/authentication.php" );
    $this->is_valid = false;
    $this->diag = "";

  }

  /**
   * Private clone method to prevent cloning of the instance of the
   * *Singleton* instance.
   *
   * @return void
   */
  private function __clone(){
  }

  /**
   * Private unserialize method to prevent unserializing of the *Singleton*
   * instance.
   *
   * @return void
   */
  private function __wakeup(){
  }

  public function setId($id){
    $this->id = $id;
  }
  public function getId(){
    return $this->id;
  }

  public function setIssuedAt($iat){
    $this->issued_at = $iat;
  }
  public function getIssuedAt(){
    return $this->issued_at;
  }

  public function setExpiration($exp){
    $this->expiration = $exp;
  }
  public function getExpiration(){
    return $this->expiration;
  }

  private function setEncToken($enc_token){
    $this->enc_token = $enc_token;
  }
  public function getEncToken(){
    return $this->enc_token;
  }

  protected function setIsValid($isValid){
    $this->is_valid = $isValid;
  }
  public function isValid(){
    return $this->is_valid;
  }

  protected function setDiagnostic($diag){
    $this->diag = $diag;
  }
  public function getDiagnostic(){
    return $this->diag;
  }


  public function build($id){
    $token_data = array();
    $token_data['id'] = $id;
    $token_data['iat'] = date('Y-m-d H:i:s');
    $token_data['exp'] = date("Y-m-d H:i:s", strtotime($token_data['iat'] . "+". $this->auth_config['TOKEN_VALIDITY'] ." minutes"));
    $this->setId($id);
    $this->setIssuedAt($token_data['iat']);
    $this->setExpiration($token_data['exp']);

    $this->enc_token = JWT::encode($token_data, $this->auth_config['SECRET_SERVER_KEY']);
    return $this->enc_token;
  }

  public static function validateToken(){

    $token = self::getInstance();

    $headers = self::parseRequestHeaders();

    if(array_key_exists('Token', $headers)){

      $token_data = JWT::decode($headers['Token'], $token->auth_config['SECRET_SERVER_KEY']); //self::$secret_server_key);

      $token->setId($token_data->id);
      $token->setIssuedAt($token_data->iat);
      $token->setExpiration($token_data->exp);

      if(strtotime($token->getIssuedAt()) > strtotime(date('Y-m-d H:i:s'))){

        $token->setIsValid(false);
        $token->setDiagnostic( "Token not valid, issued in future!" );

      }
      else{
        if(strtotime($token->getExpiration()) < strtotime(date('Y-m-d H:i:s'))){

          $token->setIsValid(false);
          $token->setDiagnostic( "Token expired" );

        }
        else{

          $token->setIsValid(true);
          $token->setDiagnostic( "Token is valid for user " . $token->id );

        }
      }

    }
    else{

      $token->setIsValid(false);
      $token->setDiagnostic( "No authorization token present" );

    }
    return $token;

  }

  private static function parseRequestHeaders() {
      $headers = array();
      foreach($_SERVER as $key => $value) {
          if (substr($key, 0, 5) <> 'HTTP_') {
              continue;
          }

          $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
          //print_r($header . '=' . $value );
          $headers[$header] = $value;
      }
      return $headers;
  }


}

/*
class auth_helper{

  public static function buildToken($id){
    $auth_config = include( __ROOT__."/backend/api/application/config/authentication.php" );
    $token = array();
    $token['id'] = $id;
    $token['iat'] = date('Y-m-d H:i:s');
    $token['exp'] = date("Y-m-d H:i:s", strtotime($token['iat'] . "+". $auth_config['TOKEN_VALIDITY'] ." minutes"));
    $enc_token = JWT::encode($token, $auth_config['SECRET_SERVER_KEY']); //self::$secret_server_key);
    return $enc_token;
  }

  private static function parseRequestHeaders() {
      $headers = array();
      foreach($_SERVER as $key => $value) {
          if (substr($key, 0, 5) <> 'HTTP_') {
              continue;
          }

          $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
          //print_r($header . '=' . $value );
          $headers[$header] = $value;
      }
      return $headers;
  }


  public static function validate_token(){
    $auth_config = include( __ROOT__."/backend/api/application/config/authentication.php" );

    $headers = self::parseRequestHeaders();

    if(array_key_exists('Token', $headers)){ //$headers['Token']){
      $token = JWT::decode($headers['Token'], $auth_config['SECRET_SERVER_KEY']); //self::$secret_server_key);

      //print_r('IAT =' . strtotime($token->iat) );
      //print_r('EXP =' . strtotime($token->exp) );
      //print_r('DATE =' . strtotime(date('Y-m-d H:i:s')) );

      if(strtotime($token->iat) > strtotime(date('Y-m-d H:i:s'))){
        $error = array('status' => "Error", "msg" => "Token not valid, issued in future!" );
        return $error;
        //$this->response($this->json($error), 400);
      }
      else{
        if(strtotime($token->exp) < strtotime(date('Y-m-d H:i:s'))){
          $error = array('status' => "Error", "msg" => "Token expired" );
          return $error;
          //$this->response($this->json($error), 400);
        }
        else{
          $response = array('status' => "Ok", "msg" => "Token is valid for user " . $token->id );
          return $response;
          //$this->response($this->json($response), 200);
        }
      }
      //echo $token->id;
    }
    else{
      $error = array('status' => "Error", "msg" => "No authorization token present" );
      return $error;
      //$this->response($this->json($error), 400);
    }

  }

}
*/
 ?>
