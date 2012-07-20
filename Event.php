<?php

namespace PetakUmpet;

use PetakUmpet\Database\Builder;

class Event {

  public static function log($message='')
  {
    $request = Singleton::acquire('\\PetakUmpet\\Request');
    $session = Singleton::acquire('\\PetakUmpet\\Session');

    $db = Singleton::acquire('\\PetakUmpet\\Database');

    $builder = new Builder('event', $db);

    $time = new \DateTime();
    
    $data = array(
        'user_id' => $session->getUserid(),
        'application' => $request->getModule(),
        'action' => $request->getAction(),
        'event' => $message,
        'created_at' => $time->format('Y-m-d H:i:s'),
      );

    $builder->import($data);
    $builder->save();
  }

  
}