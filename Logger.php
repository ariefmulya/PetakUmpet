<?php

namespace PetakUmpet;

class Logger {
  const INFO  = 1;
  const DEBUG = 2;

  public static function log($message='', $level=self::INFO)
  {
    $marker = array(
      self::INFO => 'PetakUmpet INFO: ',
      self::DEBUG => 'PetakUmpet DEBUG: ',
    );

    $logfile = PU_DIR . DS . 'res' . DS . 'log' . DS . 'app.log';
    $curtime = new \DateTime;

    file_put_contents($logfile, '['. $curtime->format('Y-m-d H:i:s') . ']' . $marker[$level] . $message . "\n", FILE_APPEND | LOCK_EX);
  }

  public static function getLogContents()
  {
    $logfile = PU_DIR . DS . 'res' . DS . 'log' . DS . 'app.log';
    echo file_get_contents($logfile);
  }
  
}