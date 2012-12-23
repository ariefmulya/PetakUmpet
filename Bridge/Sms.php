<?php

namespace PetakUmpet\Bridge;

// the use of this bridge require SmsTools library 
// download from life2play.net / contact: info@lif2play.net

use SmsGateway\SmsTools;

use PetakUmpet\Database\Model;
use PetakUmpet\Database\Accessor;
use PetakUmpet\Pager\TablePager;

abstract class Sms {

  static public function inbox($request, $rows)
  {
    $messages = SmsTools::scanInbox();
    if (count($messages) > 0) {
      $smsInbox = new Model('sms_inbox');
      $smsInbox->insert($messages);
    }

    $pager = new TablePager($request, $rows);
    $pager->setReadOnly(true);
    $pager->setOrderBy('filetime_at', false);
    $pager->build('sms_inbox', array('from', 'received', 'content'));

    return $pager;
  }

  static public function outbox($request, $rows)
  {
    $dba = new Accessor('sms_outbox');

    $messages = $dba->findBy(array('sent_at' => null));

    foreach ($messages as $m) {
      $sentAt = SmsTools::getSentStatus($m['smsfile']);
      if ($sentAt) {
        $m['sent_at'] = $sentAt;
          $dba->update($m, array('id' => $m['id']));
      }
    }

    $pager = new TablePager($request,$rows);
    $pager->setReadOnly(true);
    $pager->setOrderBy('created_at', false);
    $pager->build('sms_outbox', array('destination', 'content', 'sent_at'));

    return $pager;
  }

  static public function send($numbers, $content)
  {
    $numbers = explode(";", $numbers);

    foreach ($numbers as $n) {
      $n = trim($n);
      $fname = SmsTools::send($n, $content);
      if ($fname) {
        $data['destination'] = $n;
        $data['content'] = $content;
        $data['smsfile'] = $fname;
        $smsOutbox = new Model('sms_outbox');
        $smsOutbox->insert($data);
      }
    }

    return true;
  }


}