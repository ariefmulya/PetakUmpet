<?php

namespace PetakUmpet\Bridge;
use PetakUmpet\Singleton;

require_once (PU_DIR . DS . 'lib' . DS . 'SwiftMailer' . DS . 'swift_required.php');

abstract class Email {

  static public function send($subject, $to, $body, $type='text/plain')
  {
    $config = Singleton::acquire('\\PetakUmpet\\Config'); 

    $emailConf = $config->getEmailConfig();
    extract($emailConf);

    $transport = \Swift_SmtpTransport::newInstance($server, $port)->setUsername($username)->setPassword($password);
    if (isset($encryption) && $encryption != '') {
      $transport->setEncryption($encryption);
    }
    $mailer    = \Swift_Mailer::newInstance($transport);
    $mailer->from = $from;

    $message = \Swift_Message::newInstance($subject)->setFrom($mailer->from)->setTo($to)->setBody($body, $type);

    return $mailer->send($message);
  }

}