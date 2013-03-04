<?php

namespace PetakUmpet\Bridge;

class IPaymu {

  private $url;
  private $key;
  private $action;
  private $format;

  private $returnUrl;
  private $notifyUrl;
  private $cancelUrl;

  private $product;
  private $price;
  private $quantity;
  private $comments;

  private $paypalEmail;
  private $paypalPrice;
  private $invoiceNumber;

  public function __construct()
  {
    $this->url    = 'https://my.ipaymu.com/payment.htm';
    $this->action = 'payment';
    $this->format = 'json';
    $this->comments = '';
  }

  public function setKey($v) { $this->key = $v; }
  public function setAction($v) { $this->action = $v; }
  public function setFormat($v) { $this->format = $v; }

  public function setReturnUrl($v) { $this->returnUrl = $v; }
  public function setNotifyUrl($v) { $this->notifyUrl = $v; }
  public function setCancelUrl($v) { $this->cancelUrl = $v; }

  public function setProduct($v) { $this->product = $v; }
  public function setPrice($v) { $this->price = $v; }
  public function setQuantity($v) { $this->quantity = $v; }
  public function setComments($v) { $this->comments = $v; }

  public function setPaypalEmail($v) { $this->paypalEmail = $v; }
  public function setPaypalPrice($v) { $this->paypalPrice = $v; }
  public function setInvoiceNumber($v) { $this->invoiceNumber = $v; }

  public function process()
  {
    $params = array(
      'key'      => $this->key,
      'action'   => $this->action,
      'product'  => $this->product,
      'price'    => $this->price,
      'quantity' => $this->quantity,
      'comments' => $this->comments,
      'ureturn'  => $this->returnUrl,
      'unotify'  => $this->notifyUrl,
      'ucancel'  => $this->cancelUrl,
      'format'   => $this->format,
    );

    if (isset($this->paypalEmail) && isset($this->paypalPrice)) {
      $params['paypal_email']   = $this->paypalEmail;
      $params['paypal_price']   = $this->paypalPrice;
      $params['invoice_number'] = $this->invoiceNumber;
    }

    $params_string = http_build_query($params);
    //open connection
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $this->url);
    curl_setopt($ch, CURLOPT_POST, count($params));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    //execute post
    $request = curl_exec($ch);

    if ( $request === false ) {
        echo 'Curl Error: ' . curl_error($ch);
    } else {
      $result = json_decode($request, true);
      if( isset($result['url']) )
          header('location: '. $result['url']);
      else {
          echo "Request Error ". $result['Status'] .": ". $result['Keterangan'];
      }
    }

    //close connection
    curl_close($ch);
  }

} 