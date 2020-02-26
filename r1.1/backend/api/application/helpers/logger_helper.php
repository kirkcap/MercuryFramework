<?php
/*
Copyright 2016 Wilson Rodrigo dos Santos - wilson.santos@gmail.com

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/
/**
* PHP version 5
*
* @category Logging
* @package  com\mercuryfw\helpers
* @author   Wilson Rodrigo dos Santos <wilson.santos@gmail.com>
* @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
* @link     https://github.com/kirkcap/MercuryFramework
*/

namespace com\mercuryfw\helpers;
use com\mercuryfw\models\configModel as configModel;
use com\mercuryfw\helpers\Utils as Utils;
use \ReflectionClass as ReflectionClass;
use \DateTime as DateTime;
use \DateTimeZone as DateTimeZone;

class Logger{
  const LOG_TYPE_Success = 'S';
  const LOG_TYPE_Info = 'I';
  const LOG_TYPE_Warning = 'W';
  const LOG_TYPE_Error = 'E';

  private $log_option;
  private $log_saver_class;
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

    $cfgModel = new configModel("admin_cfg");

    $log_option = $cfgModel->findByKey("log_option");
    $this->log_option = sizeOf($log_option)>0?$log_option['log_option']:'';

    $log_saver_class_name = $cfgModel->findByKey("log_saver_class");
    $this->log_saver_class_name = sizeOf($log_saver_class_name) == 0?"FileLogSaver":$log_saver_class_name['log_saver_class'];

    $destination = $cfgModel->findByKey("log_saver_destination");
    $log_saver_destination = sizeOf($destination)==0?'Log':$destination['log_saver_destination'];

    if($this->log_option!='' && $this->log_saver_class_name!='' && $log_saver_destination!=''){
      $reflectionClass = new ReflectionClass('com\\mercuryfw\\helpers\\'.$this->log_saver_class_name);
      $this->log_saver_class = $reflectionClass->newInstanceArgs([$log_saver_destination]);
    }
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

  public function log($type, $arr_params = [], $message = ''){
    $log = false;
    if($this->log_option){
      switch($this->log_option){
        case 'all':
          $log = true;
          break;
        case 'error':
          if($type!=null && $type==Logger::LOG_TYPE_Error){
            $log = true;
          }
          break;
        case 'warning':
          if($type!=null && ( $type==Logger::LOG_TYPE_Warning || $type==Logger::LOG_TYPE_Error ) ){
            $log = true;
          }
          break;
        case 'info':
          if($type!=null && ( $type==Logger::LOG_TYPE_Info || $type==Logger::LOG_TYPE_Warning || $type==Logger::LOG_TYPE_Error ) ){
            $log = true;
          }
          break;
      }
    }
    if($log){
      $this->log_saver_class->save($type, $arr_params, $message);
    }
  }

}


interface iLogSaver{

  public function __construct($destination);

  public function save($type, $arr_params, $message);

  //private function load();

}

class FileLogSaver implements iLogSaver{
  private $log_path;
  private $log_file;
  private $log_contents;

  public function __construct($destination){
    $this->log_path = __ROOT__."/backend/log/";
    $serverTimezone = date_default_timezone_get();//Obtaining Server Timezone ID
    $currDate = new DateTime(date('Y-m-d'), new DateTimeZone($serverTimezone)); //Obtaining current date and time in the server timezone
    $this->log_file = $this->log_path . $destination .'_'. $currDate->format('Y-m-d') . '.html';
    //$this->log_contents = $this->load();
  }

  public function save($type, $arr_params = [], $message = ''){
    $log_timestamp = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone(date_default_timezone_get()));
    $log_type = 'undefined';
    if($type!=null){
      switch($type){
        case Logger::LOG_TYPE_Success:
          $log_type = 'Success';
          break;
        case Logger::LOG_TYPE_Info:
          $log_type = 'Info';
          break;
        case  Logger::LOG_TYPE_Warning:
          $log_type = 'Warning';
          break;
        case  Logger::LOG_TYPE_Error:
          $log_type = 'Error';
          break;
        case Others:
          $log_type = 'undefined';
          break;
      }
    }
    $log_var_data = "";

    if($arr_params && sizeOf($arr_params)>0){
      foreach($arr_params as $name => $contents){
        $log_var_data .= '<p>Contents for parameter ' . $name . ':' . Utils::debug_ret($contents, $name, false) .'</p>';
      }
    }

    $this->log_contents = "\r\n<br><p>"."Log Timestamp:".$log_timestamp->format('Y-m-d H:i:s')."</p>\r\n<p>"."</p>\r\n<p>"."Log Type:".$log_type."</p>\r\n<p>"."LogContents:".$log_var_data."</p>";
    if($message!=''){
      $this->log_contents .= "\r\n" . "<p>Message:" .$message ."</p>";
    }
    file_put_contents($this->log_file, $this->log_contents, FILE_APPEND);
  }
}
 ?>
