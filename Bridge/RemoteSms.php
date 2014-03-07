<?php

namespace PetakUmpet\Bridge;

// the use of this bridge require SmsTools library 
// download from life2play.net / contact: info@life2play.net

use SmsGateway\RemoteSmsTools;

use PetakUmpet\Database\Model;

abstract class RemoteSms {

  static public function send($numbers, $content, $save=false)
  {
    $numbers = explode(";", $numbers);

    foreach ($numbers as $n) {
      $n = trim($n);
      $fname = RemoteSmsTools::send($n, $content);
      if ($fname && $save) {
        $data['destination'] = $n;
        $data['content'] = $content;
        $data['smsfile'] = $fname;
        $smsOutbox = new Model('sms_outbox');
        $smsOutbox->save($data);
      }
    }

    return true;
  }


}