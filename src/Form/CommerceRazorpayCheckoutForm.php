<?php

/**
 * @file
 * Contains \Drupal\commerce_razorpay\Form\CommerceRazorpayCheckoutForm.
 */

namespace Drupal\commerce_razorpay\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
// use Razorpay\Api\Api;
use Drupal\RazorpayPhp\Api;
require 'vendor/autoload.php';

class CommerceRazorpayCheckoutForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'commerce_razorpay_checkout_form';
  }

  public function create_an_order() {
    $fb_lib_path = function_exists('libraries_get_path') ? libraries_get_path('razorpay-php') : 'sites/all/libraries/razorpay-php';
    // $fb_lib_path = library_load('razorpay-php');
    \Drupal::logger('fb lib path')->notice($fb_lib_path);


    //  $fb_platform = $fb_lib_path . '/src/Api.php';
 // watchdog('commerce_razorpay fb platform', $fb_platform);
 // dpm("fb platform");
 // dpm($fb_platform);
 // include($fb_lib_path);
 // include($fb_lib_path.'/Razorpay.php');
 // include('sites/all/libraries/razorpay-php/Razorpay.php');
 // include($fb_lib_path.'/src/Api.php');
 // libraries_load($fb_lib_path);
 // require(libraries_get_path('razorpay-php') . '/Api.php');
 // include_once($fb_lib_path);
 // include_once('/Applications/MAMP/bin/php/php5.6.10/lib/php');
 // require_once DRUPAL_ROOT ."/".$fb_lib_path;
 // libraries_load('razorpay-php');
 // require($fb_lib_path.'/src/Api.php');
 // drupal_add_library('commerce_razorpay', 'razorpay-php', TRUE);
 // watchdog('commerce_razorpay - api', !class_exists('Api'));
 // watchdog('commerce_razorpay - include', !@include($fb_platform));
 //
 //
//    require_once $fb_lib_path;
    // require_once DRUPAL_ROOT ."/". $fb_lib_path.'/src/Api.php';
    $api = new Api('rzp_test_26ccbdbfe0e84b', '69b2e24411e384f91213f22a');
    $order = $api->order->create(array('amount' => 100, "currency", "INR", "receipt", "test_1", "payment_capture", false));


// $refund = $payment->create();

    return $order;
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    // drupal_add_js(drupal_get_path('module', 'commerce_razorpay') . '/commerce_razorpay.js');
// drupal_add_js('https://checkout.razorpay.com/v1/checkout.js');
    $order = $this->create_an_order();

    // print '<pre>';
    // print_r("order");
    // print '</pre>';
    // print '<pre>';
    // print_r($order);
    // print '</pre>';

    $path1 = drupal_get_path('module', 'commerce_razorpay') . '/custom1.js';
    $path1 = 'https://checkout.razorpay.com/v1/checkout.js';

//    $form['test'] = [
//      '#type' => 'markup',
//      '#markup' => '<script src= "https://checkout.razorpay.com/v1/checkout.js"
//    data-key="rzp_test_ipkgumBJtJrvd1"
//    data-amount="5000"
//    data-buttontext="Pay with Razorpay"
//    data-name="Merchant Name"
//    data-description="Purchase Description"
//    data-image="https://your-awesome-site.com/your_logo.jpg"
//    data-prefill.name="Harshil Mathur"
//    data-prefill.email="support@razorpay.com"
//    data-theme.color="#F37254"
//    ></script>',
//    ];
    $form['hidden'] = [
      '#type' => 'hidden',
      '#default_value' => 'Hidden Element',
    ];

    // @FIXME
    // // @FIXME
    // // This looks like another module's variable. You'll need to rewrite this call
    // // to ensure that it uses the correct configuration object.
    // $form['message'] = array(
    //     '#type' => 'item',
    //     '#title' => t('Email address in use'),
    //     '#markup' => t('There is already an account associated with your GitHub email address. Type your !site account password to merge accounts.', array('!site' => variable_get('site_name'))),
    //   );

    // $form['#action'] = '/purchase';
//    $form['submit'] = [
//      '#type' => 'submit',
//      '#value' => 'Submit',
//    ];
//    $form['#submit'][] = 'commerce_razorpay_checkout_submit';
    // dpm("form");
    // dpm($form);

    // $form['#attached']['js'] = drupal_add_js(drupal_get_path('module', 'commerce_razorpay') . '/commerce_razorpay.js');
    // $form['#attached']['js'] =  '/commerce_razorpay.js';
    return $form;
  }

  /**
   * Implements hook_form_submit().
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }
}
