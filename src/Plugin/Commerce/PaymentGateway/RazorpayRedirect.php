<?php

namespace Drupal\commerce_razorpay\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides the Off-site Redirect payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "razorpay_redirect",
 *   label = "Razorpay Redirect",
 *   display_label = "Razorpay Redirect",
 *    forms = {
 *     "offsite-payment" = "Drupal\commerce_razorpay\PluginForm\OffsiteRedirect\RazorpayForm",
 *   },
 *   payment_method_types = {"credit_card"},
 *   credit_card_types = {
 *     "amex", "dinersclub", "discover", "jcb", "maestro", "mastercard", "visa",
 *   },
 * )
 */
class RazorpayRedirect extends OffsitePaymentGatewayBase {


  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $key_id = $this->configuration['key_id'];
    $key_secret = $this->configuration['key_secret'];

    $form['key_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Key Id'),
      '#default_value' => $key_id,
      '#required' => TRUE,
    ];
    $form['key_secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Key Secret'),
      '#default_value' => $key_secret,
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['key_id'] = $values['key_id'];
      $this->configuration['key_secret'] = $values['key_secret'];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function onReturn(OrderInterface $order, Request $request) {
//    print '<pre>'; print_r("request"); print '</pre>';
//    print '<pre>'; print_r($request); print '</pre>';exit;

    $additionalCharges = $request->get('additionalCharges');
    $status = $request->get('status');
    $firstname = $request->get('firstname');
    $txnid = $request->get('txnid');
    $amount = $request->get('amount');
    $posted_hash = $request->get('hash');
    $key = $request->get('key');
    $productinfo = $request->get('productinfo');
    $email = $request->get('email');
    $salt = $this->configuration['psalt'];

    $payment_storage = $this->entityTypeManager->getStorage('commerce_payment');
    $payment = $payment_storage->create([
      'state' => $status,
      'amount' => $order->getTotalPrice(),
      'payment_gateway' => $this->entityId,
      'order_id' => $order->id(),
      'test' => $this->getMode() == 'test',
      'remote_id' => $txnid,
      'remote_state' => $request->get('payment_status'),
      'authorized' => REQUEST_TIME,
    ]);
    $payment->save();
    drupal_set_message($this->t('Your payment was successful with Order id : @orderid and Transaction id : @transaction_id', ['@orderid' => $order->id(), '@transaction_id' => $txnid]));

  }

  /**
   * {@inheritdoc}
   */
  public function onCancel(OrderInterface $order, Request $request) {
    $status = $request->get('status');
    drupal_set_message($this->t('Payment @status on @gateway but may resume the checkout process here when you are ready.', [
      '@status' => $status,
      '@gateway' => $this->getDisplayLabel(),
    ]), 'error');
  }

}
