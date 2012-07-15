<?php
/* 
 * Mail handler
 */


include_once('../lib/swift/lib/swift_required.php');

function pu_email_setup($server='smtp.life2play.net', $port='25', $username='arief@life2play.net', $password='4ngk4s4lu4r', $from=array('relay@life2play.net' => 'Life2play Relay'))
{
  global $mailer;

  if (!isset($_SESSION['mailer'])) {
    $_SESSION['mailer'] = $mailer;
  } else {
    $mailer = $_SESSION['mailer'];
  }

  $transport = Swift_SmtpTransport::newInstance($server, $port)->setUsername($username)->setPassword($password);
  $mailer    = Swift_Mailer::newInstance($transport);
  $mailer->from = $from;

  return $mailer;
}

function pu_email_send($mailer, $subject='(no subject)', $to=array(), $body='', $type='text/plain')
{
  $message   = Swift_Message::newInstance($subject)->setFrom($mailer->from)->setTo($to)->setBody($body, $type);
  return $mailer->send($message);
}

