<?php

namespace Drupal\commerce_razorpay\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayBase;
use Drupal\Core\Form\FormStateInterface;
use Razorpay\Api\Api;
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

    $key_id = $this->configuration['key_id'];
    $key_secret = $this->configuration['key_secret'];
    $api = new Api($key_id, $key_secret);
    $payment = $api->order->fetch($order->getData('merchant_order_id'));
    $payment_object = $payment->payments();
    $status = $payment_object['items'][0]->status; // eg : refunded, captured, authorized, failed.
    $refund_status = $payment_object['items'][0]->refund_status; // eg : full, partial
    $amount_refunded = ($payment_object['items'][0]->amount_refunded)/100;
    $service_tax = $payment_object['items'][0]->service_tax;
    $amount = $payment_object['items'][0]->amount;
    // card_id
    //  @TODO Save Card details , method of payment etc.


    // Succeessful.
    $message = '';
    $remote_status = '';
    if ($status == "captured") {
      // Status is success.
      $remote_status = t('Success');
      $message = $this->t('Your payment was successful with Order id : @orderid has been received at : @date', ['@orderid' => $order->id(), '@date' => date("d-m-Y H:i:s", REQUEST_TIME)]);
      $status = COMMERCE_PAYMENT_STATUS_SUCCESS;
    }
    elseif ($status == "authorized") {
      // Batch process - Pending orders.
      $remote_status = t('Pending');
      $message = $this->t('Your payment with Order id : @orderid is pending at : @date', ['@orderid' => $order->id(), '@date' => date("d-m-Y H:i:s", REQUEST_TIME)]);
      $status = COMMERCE_PAYMENT_STATUS_PENDING;
    }
    elseif ($status == "failed") {
      // Failed transaction.
      $remote_status = t('Failure');
      $message = $this->t('Your payment with Order id : @orderid failed at : @date', ['@orderid' => $order->id(), '@date' => date("d-m-Y H:i:s", REQUEST_TIME)]);
      $status = COMMERCE_PAYMENT_STATUS_FAILURE;
    }


    $payment_storage = $this->entityTypeManager->getStorage('commerce_payment');
    $payment = $payment_storage->create([
        'state' => $status,
        'amount' => $order->getTotalPrice(),
        'payment_gateway' => $this->entityId,
        'order_id' => $order->id(),
        'test' => $this->getMode() == 'test',
        'remote_id' => $payment_object['items'][0]->id,
        'remote_state' => $remote_status ? $remote_status : $request->get('payment_status'),
        'authorized' => REQUEST_TIME,
      ]
    );

    $payment->save();
    drupal_set_message($message);

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
