<?php

namespace Drupal\commerce_razorpay\PluginForm\OffsiteRedirect;

use Drupal\commerce_payment\PluginForm\PaymentOffsiteForm as BasePaymentOffsiteForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\commerce_order\Entity\Order;

/**
 * Provides the Off-site payment form.
 */
class RazorpayForm extends BasePaymentOffsiteForm {


  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['#attached'] =[
      'library' => ['commerce_razorpay/commerce_razorpay.payment']
    ];


//    $form['#attached']['library'] =  ['commerce_razorpay/commerce_razorpay.payment'];

    /** @var \Drupal\commerce_payment\Entity\PaymentInterface $payment */
    $payment = $this->entity;
//    print '<pre>'; print_r("this entity"); print '</pre>';
//    print '<pre>'; print_r($payment); print '</pre>';exit;

    $redirect_method = 'post';
    /** @var \Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayInterface $payment_gateway_plugin */
    $payment_gateway_plugin = $payment->getPaymentGateway()->getPlugin();

    //$owner = \Drupal::routeMatch()->getParameter('commerce_order')->getCustomer();
    $order_id = \Drupal::routeMatch()->getParameter('commerce_order')->id();
    $order = Order::load($order_id);

    $key_id = $payment_gateway_plugin->getConfiguration()['key_id'];
    $key_secret = $payment_gateway_plugin->getConfiguration()['key_secret'];


//    $billing_profile = $order->getBillingProfile();
//    $address = $order->getBillingProfile()->address->first();




//    print '<pre>'; print_r("order"); print '</pre>';
//    print '<pre>'; print_r($order); print '</pre>';exit;



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
