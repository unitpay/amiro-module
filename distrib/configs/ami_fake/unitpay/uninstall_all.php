<?php
/**
 * Script executing on hard uninstalling.
 *
 * @copyright Wallet One. All rights reserved.
 * @category  eshop
 * @package   Config_eshop_w1
 */

$destPath = AMI_Registry::get('path/root') . '_local/eshop/pay_drivers/unitpay/';
unlink($destPath . 'gate_unitpay.php');

$destPath = AMI_Registry::get('path/root') . 'drivers/';
unlink($destPath . 'gate_unitpay.php');

$oStorage = AMI::getResource('storage/fs');
$oTplStorage = AMI::getResource('storage/tpl');

foreach(
    array(
        AMI_iTemplate::LNG_PATH       . '/eshop_purchase.lng',
        AMI_iTemplate::LOCAL_LNG_PATH . '/eshop_order.lng'
    ) as $target
){
    $this->aTx['storage']->addCommand(
        'tpl/uninstall',
        new AMI_Tx_Cmd_Args(
            array(
                'mode'     => $this->oArgs->mode,
                'modId'    => $this->oArgs->modId,
                'target'   => $target,
                'oStorage' => $oTplStorage
            )
        )
    );
}

// delete driver files
$destPath = AMI_Registry::get('path/root') . '_local/eshop/pay_drivers/unitpay/';
foreach(
    array(
        'driver.php',
        'driver.tpl',
        'driver.lng',
    ) as $file
){
    $this->aTx['storage']->addCommand(
        'storage/clean',
        new AMI_Tx_Cmd_Args(
            array(
                'modId'      => $this->oArgs->modId,
                'mode'       => $this->oArgs->mode,
                'target'     => $destPath . $file,
                'oStorage'   => $oStorage,
                'rmEmptyDir' => TRUE
            )
        )
    );
}
