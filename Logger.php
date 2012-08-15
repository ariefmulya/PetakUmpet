<?php

namespace PetakUmpet;

class Logger {
  const NONE  = 0;
  const INFO  = 1;
  const DEBUG = 2;

  public static function log($message='', $level=self::INFO)
  {
    $marker = array(
      self::INFO => '[PetakUmpet INFO ] ',
      self::DEBUG => '[PetakUmpet DEBUG] ',
    );

    $request = Singleton::acquire('\\PetakUmpet\\Request');
    $config = Singleton::acquire('\\PetakUmpet\\Config');

    $logfile = PU_DIR . DS . 'app' . DS . $request->getApplication() . DS . 'res' . DS . 'log' . DS . 'app.log';
    $curtime = new \DateTime;

    if ($config->getLogLevel() >= $level) {
      if (!is_file($logfile) || !is_writable($logfile)) {
        die ("Logger Error, have you setup logfile and access mode correctly?");
      }
      file_put_contents($logfile, '['. $curtime->format('Y-m-d H:i:s') . ']' . $marker[$level] . $message . "\n", FILE_APPEND | LOCK_EX);
    }
  }
  
}