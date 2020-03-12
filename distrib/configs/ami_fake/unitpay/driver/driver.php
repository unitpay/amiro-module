<?php

class Unitpay_PaymentSystemDriver extends AMI_PaymentSystemDriver{

  protected $driverName = 'unitpay';
  /**
   * Get checkout button HTML form
   *
   * @param array $aRes Will contain "error" (error description, 'Success by default') and "errno" (error code, 0 by default). "forms" will contain a created form
   * @param array $aData The data list for button generation
   * @param bool $bAutoRedirect If form autosubmit required (directly from checkout page)
   * @return bool true if form is generated, false otherwise
   */
  public function getPayButton(&$aRes, $aData, $bAutoRedirect = false){
    $aRes['errno'] = 0;
    $aRes['error'] = 'Success';

    $aData['hiddens'] = $this->getScopeAsFormHiddenFields($aData);

    // Set your fields of $aData here
    return parent::getPayButton($aRes, $aData, $bAutoRedirect);
  }

  /**
   * Get the form that will be autosubmitted to payment system. This step is required for some shoping cart actions.
   *
   * @param array $aData The data list for button generation
   * @param array $aRes Will contain "error" (error description, 'Success by default') and "errno" (error code, 0 by default). "forms" will contain a created form
   * @return bool true if form is generated, false otherwise
   */
  public function getPayButtonParams($aData, &$aRes){

    $sum = $aData['amount'];
    $sum = number_format($sum, 2, ".", "");
    $desc = 'Заказ №' . $aData['order'];
    $account = $aData['order'];

    $aData['payment_url'] = 'https://'.$aData['DOMAIN'].'/pay/' . $aData['PUBLIC_KEY'] .
        '?' . 'sum=' . $sum .
        '&' . 'desc=' . $desc .
        '&' . 'account=' . $account;

    return parent::getPayButtonParams($aData, $aRes);
  }

  /**
   * Verify the order from user back link. In success case 'accepted' status will be setup for order.
   *
   * @param array $aGet $_GET data
   * @param array $aPost $_POST data
   * @param array $aRes reserved array reference
   * @param array $aCheckData Data that provided in driver configuration
   * @param array $aOrderData order data that contains such fields as id, total, order_date, status
   * @return bool true if order is correct and false otherwise
   * @see AMI_PaymentSystemDriver::payProcess(...)
   */
  public function payProcess($aGet, $aPost, &$aRes, $aCheckData, $aOrderData){
    // See implplementation of this method in parent class
    return parent::payProcess($aGet, $aPost, $aRes, $aCheckData, $aOrderData);
  }

  /**
   * Verify the order by payment system background responce. In success case 'confirmed' status will be setup for order.
   *
   * @param array $aGet $_GET data
   * @param array $aPost $_POST data
   * @param array $aRes reserved array reference
   * @param array $aCheckData Data that provided in driver configuration
   * @param array $aOrderData order data that contains such fields as id, total, order_date, status
   * @return int -1 - ignore post, 0 - reject(cancel) order, 1 - confirm order
   * @see AMI_PaymentSystemDriver::payCallback(...)
   */
  public function payCallback($aGet, $aPost, &$aRes, $aCheckData, $aOrderData)
  {
    if (!@is_array($aGet)) {
      $aGet = array();
    }

    $data = $aGet;
    $method = '';
    $params = array();
    if ((isset($data['params'])) && (isset($data['method'])) && (isset($data['params']['signature']))) {
      $params = $data['params'];
      $method = $data['method'];
      $signature = $params['signature'];
      if (empty($signature)) {
        $status_sign = false;
      } else {
        $status_sign = $this->verifySignature($params, $method, $aCheckData['SECRET_KEY']);
      }
    } else {
      $status_sign = false;
    }
//    $status_sign = true;
    if ($status_sign) {
      switch ($method) {
        case 'check':
          $result = $this->check($params);
          break;
        case 'pay':
          $result = $this->pay($params);
          break;
        case 'error':
          $result = $this->error($params);
          break;
        default:
          $result = array('error' =>
              array('message' => 'неверный метод')
          );
          break;
      }
    } else {
      $result = array('error' =>
          array('message' => 'неверная сигнатура')
      );
    }
    $this->hardReturnJson($result);

  }

  function check( $params )
  {
    $order_id = $params['account'];
    $order = AMI::getResourceModel('eshop_order/table')->find($order_id);
    $total = $order->total;
    if ((float)$total != (float)$params['orderSum']) {
      $result = array('error' =>
          array('message' => 'не совпадает сумма заказа')
      );
    }elseif ('RUB' != $params['orderCurrency']) {
      $result = array('error' =>
          array('message' => 'не совпадает валюта заказа')
      );
    }
    else{
      $result = array('result' =>
          array('message' => 'Запрос успешно обработан')
      );
    }

    return $result;

  }

  function pay( $params )
  {
    $order_id = $params['account'];
    $order = AMI::getResourceModel('eshop_order/table')->find($order_id);
    $total = $order->total;

    if ((float)$total != (float)$params['orderSum']) {
      $result = array('error' =>
          array('message' => 'не совпадает сумма заказа')
      );
    }elseif ('RUB' != $params['orderCurrency']) {
      $result = array('error' =>
          array('message' => 'не совпадает валюта заказа')
      );
    }
    else{
      global $cms, $oOrder;
      $oOrder->updateStatus($cms, $order_id, 'auto', 'confirmed');
      $this->onPaymentConfirmed($order_id);

      $result = array('result' =>
          array('message' => 'Запрос успешно обработан')
      );
    }
    return $result;
  }

  function error( $params )
  {

    $order_id = $params['account'];
    global $cms, $oOrder;

    $oOrder->updateStatus($cms, $order_id, 'auto', 'rejected');
    $this->onPaymentConfirmed($order_id);

    $result = array('result' =>
        array('message' => 'Запрос успешно обработан')
    );
    return $result;
  }

  function getSignature($method, array $params, $secretKey)
  {
    ksort($params);
    unset($params['sign']);
    unset($params['signature']);
    array_push($params, $secretKey);
    array_unshift($params, $method);
    return hash('sha256', join('{up}', $params));
  }

  function verifySignature($params, $method, $secret)
  {
    return $params['signature'] == $this->getSignature($method, $params, $secret);
  }

  function hardReturnJson( $arr )
  {
    header('Content-Type: application/json');
    $result = json_encode($arr);
    die($result);
  }

}
