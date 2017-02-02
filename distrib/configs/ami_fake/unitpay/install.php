<?php
/**
 * Script executing on installing.
 *
 * @copyright Wallet One. All rights reserved.
 * @category  eshop
 * @package   Config_eshop_w1
 */

$srcPath = dirname(__FILE__) . '/';
$oStorage = AMI::getResource('storage/fs');
$oTplStorage = AMI::getResource('storage/tpl');

// copy driver files
$destPath = AMI_Registry::get('path/root') . '_local/eshop/pay_drivers/unitpay/';
$oStorage->setMode(AMI_iStorage::MODE_MAKE_FOLDER_ON_COPY);
foreach(
    array(
        'driver.php',
        'driver.tpl',
        'driver.lng',
    ) as $file
){
    $this->aTx['storage']->addCommand(
        'storage/copy',
        new AMI_Tx_Cmd_Args(
            array(
                'mode'     => $this->oArgs->mode,
                'source'   => $srcPath . 'driver/' . $file,
                'target'   => $destPath . $file,
                'oStorage' => $oStorage
            )
        )
    );
}


foreach(
    array(
        $srcPath . 'eshop_purchase.lng' => AMI_iTemplate::LNG_PATH . '/eshop_purchase.lng',
        $srcPath . 'eshop_order.lng'    => AMI_iTemplate::LOCAL_LNG_PATH . '/eshop_order.lng'
    ) as $src => $dest
){
    $this->aTx['storage']->addCommand(
        'tpl/install',
        new AMI_Tx_Cmd_Args(
            array(
                'mode'      => $this->oArgs->mode,
                'modId'     => $this->oArgs->modId,
                'target'    => $dest,
                'content'   => $oStorage->load($src),
                'oStorage'  => $oTplStorage
            )
        )
    );
}