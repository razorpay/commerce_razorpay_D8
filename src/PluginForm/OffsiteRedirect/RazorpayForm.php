<?php

namespace Drupal\commerce_razorpay\PluginForm\OffsiteRedirect;

use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_payment\PluginForm\PaymentOffsiteForm as BasePaymentOffsiteForm;
use Drupal\Core\Form\FormStateInterface;
use Psy\Exception\Exception;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use Symfony\Component\ExpressionLanguage\Tests\Node\Obj;

/**
 * Provides the Off-site payment form.
 */
class RazorpayForm extends BasePaymentOffsiteForm {


  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    global $base_url;


//    $form['#attached']['library'][] = 'commerce_razorpay/commerce_razorpay.payment';

//    $form['#attached'] =[
//      'library' => ['commerce_razorpay/commerce_razorpay.payment']
//    ];
//    $form['#attached']['js'][] = 'https://checkout.razorpay.com/v1/checkout.js';


    /** @var \Drupal\commerce_payment\Entity\PaymentInterface $payment */
    $payment = $this->entity;

    $redirect_method = 'post';
    /** @var \Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayInterface $payment_gateway_plugin */
    $payment_gateway_plugin = $payment->getPaymentGateway()->getPlugin();

//    print '<pre>'; print_r("payment gateway plugin"); print '</pre>';
//    print '<pre>'; print_r($payment_gateway_plugin); print '</pre>';exit;

    //$owner = \Drupal::routeMatch()->getParameter('commerce_order')->getCustomer();
    $order_id = \Drupal::routeMatch()->getParameter('commerce_order')->id();
    $order = Order::load($order_id);



//    $billing_profile = $order->getBillingProfile();
    $address = $order->getBillingProfile()->address->first();


//    print '<pre>'; print_r("order"); print '</pre>';
//    print '<pre>'; print_r($order_id); print '</pre>';exit;


    $amount = ($payment->getAmount()->getNumber()) * 100;

    $key_id = $payment_gateway_plugin->getConfiguration()['key_id'];
    $key_secret = $payment_gateway_plugin->getConfiguration()['key_secret'];
//    $base_url = '';
    $currency = $payment_gateway_plugin->getConfiguration()['currency'];
    $currency = 'INR';
    $receipt = $order_id;
    $payment_capture = FALSE;

//    $lib_path = (function_exists('libraries_get_path')) ? libraries_get_path('razorpay-php') : 'libraries/razorpay-php';
//    $platform = $lib_path . '/Razorpay.php';
////    dsm($platform);
//    $client = NULL;
//    $result = NULL;
//    require $platform;


//    try {
//      include __DIR__ . $platform;
//      if (!@include __DIR__ . $platform) {
//        \Drupal::logger('error')->error('Error Loading  Library');
//      }
//      else {
//        $api = new Api($key_id, $key_secret);
//        $razorpay_order = $api->order->create(array(
//          'amount' => $amount,
//          "currency" => $currency,
//          "receipt" => $receipt,
//          'payment_capture' => $payment_capture
//        ));
//
//        $merchant_order_id = $razorpay_order->id;
//        $merchant_order_id = '';
//
//
//      }
//    } catch (Exception $e) {
//
//      return NULL;
//    }

//    print '<pre>'; print_r("configuration"); print '</pre>';
//    print '<pre>'; print_r($payment_gateway_plugin->getConfiguration()); print '</pre>'; exit;
    $api = new Api($key_id, $key_secret);
    $razorpay_order = $api->order->create(array(
      'amount' => $amount,
      "currency" => $currency,
      "receipt" => $receipt,
      'payment_capture' => $payment_capture
    ));

    $merchant_order_id = $razorpay_order->id;

    $payment_method =$payment_gateway_plugin->getConfiguration();
    $billing_address = $address;

//    $form['#attached'] = [
//      'library' => ['commerce_razorpay/commerce_razorpay.payment']
//    ];
    $form['#attached']['library'][] = 'commerce_razorpay/commerce_razorpay.custom_payment';

    $form['#attached']['drupalSettings']['commerce_razorpay'] = array(
      'amount' => $amount,
      'key' => $key_id,
      'logo' => $base_url . "/" . drupal_get_path('module', 'commerce_razorpay') . '/logo.jpg',
      'order_id' => $merchant_order_id,
      'commerce_order_id' => $order_id,
      'payment_settings' => $payment_method,
      'billing_address' => $billing_address
    );


//    $form['#attached']['library'] =  ['commerce_razorpay/commerce_razorpay.payment'];

//    if ($mode == 'test') {
//      $redirect_url = self::PAYUMONEY_API_TEST_URL;
//    }
//    else {
//      $redirect_url = self::PAYUMONEY_API_URL;
//    }
    return $this->buildRedirectForm($form, $form_state);
  }

  protected function buildRedirectForm(array $form, FormStateInterface $form_state) {

//    $form['#attached'] =[
//      'library' => ['commerce_razorpay/commerce_razorpay.payment']
//    ];

//      $form['#attached']['library'][] = 'commerce_payment/offsite_redirect';
//      foreach ($data as $key => $value) {
//        $form[$key] = [
//          '#type' => 'hidden',
//          '#value' => $value,
//          // Ensure the correct keys by sending values from the form root.
//          '#parents' => [$key],
//        ];
//      }
//
//      // The key is prefixed with 'commerce_' to prevent conflicts with $data.
//      $form['commerce_message'] = [
//        '#markup' => '<div class="checkout-help">' . t('Please wait while you are redirected to the payment server. If nothing happens within 10 seconds, please click on the button below.') . '</div>',
//        '#weight' => -10,
//        // Plugin forms are embedded using #process, so it's too late to attach
//        // another #process to $form itself, it must be on a sub-element.
//        '#process' => [
//          [get_class($this), 'processRedirectForm'],
//        ],
//        '#action' => $redirect_url,
//      ];
//    }


    return $form;
  }

}
