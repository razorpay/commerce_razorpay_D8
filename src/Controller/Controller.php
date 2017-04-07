<?php

namespace Drupal\commerce_razorpay\Controller;
use Drupal\commerce_order\Entity\Order;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class Controller
 * @package Drupal\commerce_razorpay\Controller
 */
class Controller extends ControllerBase{

  public function capturePayment() {
    $amount = $_GET['amount'];
    $commerce_order_id = $_GET['order_id'];
    $payment_settings = json_decode($_GET['payment_settings']);
    $response = json_decode($_GET['response']);
    $razorpay_signature = $response->razorpay_signature;
    $razorpay_payment_id = $response->razorpay_payment_id;
    $razorpay_order_id = $response->razorpay_order_id;
    $key_id = $payment_settings->key_id;
    $key_secret = $payment_settings->key_secret;

    $api = new Api($key_id, $key_secret);
    $payment = $api->payment->fetch($razorpay_payment_id);
    if($payment->status == 'authorized') {
      $payment->capture(array('amount' => $amount));
    }

    $card = $api->card->fetch($payment->card_id);
//    print '<pre>'; print_r("card"); print '</pre>';
//    print '<pre>'; print_r($card); print '</pre>';exit;

//    $order = Order::load($commerce_order_id);
//    $payment_method = $payment->method;
//    $order->set('payment_method', $payment_method);
//
//    switch($payment_method) {
//      case 'card':
//        $order->setData('card_id',$payment->card_id);
//        break;
//      case 'bank';
//        $order->setData('bank',$payment->bank);
//        break;
//      case 'wallet':
//        $order->setData('wallet',$payment->wallet);
//        break;
//      case 'vpa':
//        $order->setData('vpa',$payment->vpa);
//        break;
//    }
//    $order->save();

    // Validating  Signature.
    $success = true;
    $error = "Payment Failed";

    if (empty($razorpay_payment_id) === false)
    {
      $api = new Api($key_id, $key_secret);
      try
      {
        $attributes = array(
          'razorpay_order_id' => $razorpay_order_id,
          'razorpay_payment_id' => $razorpay_payment_id,
          'razorpay_signature' => $razorpay_signature
        );
        $api->utility->verifyPaymentSignature($attributes);
      }
      catch(SignatureVerificationError $e)
      {
        $success = false;
        $error = 'Razorpay Error : ' . $e->getMessage();

      }
    }

    // If Payment is successfully captured at razorpay end.
    if ($success === true) {
      $message = "Payment ID: {$razorpay_payment_id}";
      drupal_set_message(t($message));
    }
    else {
      $message = "Your payment failed " . $error;
      drupal_set_message(t($message), 'error');
    }
    $url =  Url::fromRoute('commerce_payment.checkout.return', [
      'commerce_order' => $commerce_order_id,
      'step' => 'payment',
    ], ['absolute' => TRUE])->toString();
    return new RedirectResponse($url);

  }
}
