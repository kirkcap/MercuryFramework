<?php
namespace com\mercuryfw\helpers;

class Utils{

  public static function var_dump_ret($mixed = null) {
    ob_start();
    var_dump($mixed);
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
  }

  public static function debug_ret($mixed = null, $name = null, $plain = false){
    ob_start();
    debug($mixed, $name, $plain);
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
  }
}
 ?>
