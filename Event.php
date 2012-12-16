<?php

namespace PetakUmpet;

use PetakUmpet\Database\Accessor;

class Event {

  public static function log($message='')
  {
    $config = Singleton::acquire('\\PetakUmpet\\Config');
    if ($config->getEventLogging() === false) 
      return;
    
    $request = Singleton::acquire('\\PetakUmpet\\Request');
    $session = Singleton::acquire('\\PetakUmpet\\Session');

    $db = Singleton::acquire('\\PetakUmpet\\Database');

    if (!$db) { 
      // dont do event logging when db connection is not set
      // FIXME: how not to make Event as a class that initialize DB ?
      Logger::log("Event: no Database connection set, logging canceled.");
      return;
    }

    $dba = new Accessor('event', $db);

    $time = new \DateTime();
    
    $id = null;
    $user = $session->getUser();

    if (is_object($user)) 
      $id = $user->getId();

    $data = array(
        'user_id' => $id,
        'application' => $request->getApplication(),
        'module' => $request->getModule(),
        'action' => $request->getAction(),
        'event' => $message,
        'created_at' => $time->format('Y-m-d H:i:s'),
      );

    $dba->insert($data);
  }

  
}
