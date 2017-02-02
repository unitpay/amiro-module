<?php

class AmiFake_Unitpay_Meta extends AMI_HyperConfig_Meta{
    /**
     * Version
     *
     * @var string
     */
    protected $version = '1.0';

    /**
     * Flag specifying that hypermodule configs can have only one instance per config
     *
     * @var bool
     */
    protected $isSingleInstance = TRUE;

    /**
     * Array having locales as keys and captions as values
     *
     * @var array
     */
    protected $aTitle = array(
        'en' => 'Unitpay',
        'ru' => 'Unitpay'
    );

    /**
     * Array having locales as keys and meta data as values
     *
     * @var array
     */
    protected $aInfo = array(
        'en' => array(
            'description' => 'Unitpay payment system',
            'author'      => '<a href="https://unitpay.ru" target="_blank">Unitpay</a>'
        ),
        'ru' => array(
            'description' => 'Unitpay платежная система',
            'author'      => '<a href="https://unitpay.ru" target="_blank">Unitpay</a>'
        )
    );

    /**
     * Retrurns allowed installation/uninstallation modes.
     *
     * @param  string $type  Possible values: 'install' / 'uninstall' / '' (all)
     * @return array
     */
    public function getAllowedModes($type = ''){
        $aModes = parent::getAllowedModes();
        unset($aModes['uninstall']['soft']);
        return
            $type === '' ? $aModes : $aModes[$type];
    }
}