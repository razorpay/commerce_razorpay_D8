<?php

namespace Drupal\commerce_razorpay\Controller;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use Symfony\Component\HttpFoundation\RedirectResponse;


/**
 * Class GithubConnectController.
 *
 * @package Drupal\github_connect\Controller
 */
class Controller extends ControllerBase{

  public function capturePayment() {
    $amount = $_GET['amount'];
    $commerce_order_id = $_GET['order_id'];
    $payment_settings = json_decode($_GET['payment_settings']);

//    print '<pre>'; print_r("payment settings"); print '</pre>';
//    print '<pre>'; print_r($_GET['payment_settings']); print '</pre>';


    $response = json_decode($_GET['response']);

//    print '<pre>'; print_r("response"); print '</pre>';
//    print '<pre>'; print_r($response); print '</pre>';exit;
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

//    $order->data['razorpay_payment_id'] = $razorpay_payment_id;
    // $order->data['merchant_order_id'] = $razorpay_order_id;
//    $order->save();

//    commerce_razorpay_transaction($key_id, $key_secret, $order);
    // @TODO Save drupal commerce transaction.
    // Send the customer on to the next checkout page.

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

    // If Payment is successfully captured at razorpay end, then update
    // commerce order status to checkout_complete and redirect page to complete.
    if ($success === true) {
      $message = "Your payment was successful. Payment ID: {$razorpay_payment_id}";




      drupal_set_message(t($message));


    }
    else {

      $message = "Your payment failed " . $error;
      drupal_set_message(t($message), 'error');
//      return Url::fromRoute('commerce_payment.checkout.review', [
//        'commerce_order' => $commerce_order_id,
//        'step' => 'payment',
//      ], ['absolute' => TRUE])->toString();

    }
    $url =  Url::fromRoute('commerce_payment.checkout.return', [
      'commerce_order' => $commerce_order_id,
      'step' => 'payment',
    ], ['absolute' => TRUE])->toString();
    return new RedirectResponse($url);

  }
}
