<?php
/**
 * Script executing after installation.
 *
 * @copyright Wallet One. All rights reserved.
 */

$this->oArgs->oPkgCommon->loadStatusMessages('messages.lng');
$this->oArgs->oPkgCommon->addStatusMessage('after_install', array(), AMI_Response::STATUS_MESSAGE_WARNING);

$srcPath = str_replace('\\', '/', dirname(__FILE__) . '/');
$destPath = AMI_Registry::get('path/root') . '_local/eshop/pay_drivers/unitpay/';
if(!file_exists($destPath)) {
  mkdir($destPath);
}
copy($srcPath . 'driver/' . 'gate_unitpay.php', $destPath . 'gate_unitpay.php');

$destPath = AMI_Registry::get('path/root') . 'drivers/';
copy($srcPath . '/' . 'gate_unitpay.php', $destPath . 'gate_unitpay.php');