<?php

/**
 * E-Transactions Epayment module for Magento
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * available at : http://opensource.org/licenses/osl-3.0.php
 *
 * @package    ETransactions_Epayment
 * @copyright  Copyright (c) 2013-2014 E-Transactions
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ETransactions_Epayment_Model_Config {

    const SUBSCRIPTION_OFFER1 = 'access';
    const SUBSCRIPTION_OFFER2 = '';
    const SUBSCRIPTION_OFFER3 = 'premium';

    private $_store;
    private $_configCache = array();
    private $_configMapping = array(
        'allowedIps' => 'allowedips',
        'environment' => 'environment',
        'debug' => 'debug',
        'hmacAlgo' => 'merchant/hmacalgo',
        'hmacKey' => 'merchant/hmackey',
        'identifier' => 'merchant/identifier',
        'languages' => 'languages',
        'password' => 'merchant/password',
        'rank' => 'merchant/rank',
        'site' => 'merchant/site',
        'subscription' => 'merchant/subscription',
        'kwixoShipping' => 'kwixo/shipping'
    );
    private $_urls = array(
        'system' => array(
            'test' => array(
                'https://preprod-tpeweb.e-transactions.fr/cgi/MYchoix_pagepaiement.cgi'
            ),
            'production' => array(
                'https://tpeweb.e-transactions.fr/cgi/MYchoix_pagepaiement.cgi',
                'https://tpeweb1.e-transactions.fr/cgi/MYchoix_pagepaiement.cgi',
            ),
        ),
        'responsive' => array(
            'test' => array(
                'https://preprod-tpeweb.paybox.com/cgi/FramepagepaiementRWD.cgi'
            ),
            'production' => array(
                'https://tpeweb.paybox.com/cgi/FramepagepaiementRWD.cgi',
                'https://tpeweb1.paybox.com/cgi/FramepagepaiementRWD.cgi',
            ),
        ),
        'kwixo' => array(
            'test' => array(
                'https://preprod-tpeweb.e-transactions.fr/php/'
            ),
            'production' => array(
                'https://tpeweb.e-transactions.fr/php/',
                'https://tpeweb1.e-transactions.fr/php/',
            ),
        ),
        'ancv' => array(
            'test' => array(
                'https://preprod-tpeweb.e-transactions.fr/php/'
            ),
            'production' => array(
                'https://tpeweb.e-transactions.fr/php/',
                'https://tpeweb1.e-transactions.fr/php/',
            ),
        ),
        'mobile' => array(
            'test' => array(
                'https://preprod-tpeweb.e-transactions.fr/cgi/MYframepagepaiement_ip.cgi'
            ),
            'production' => array(
                'https://tpeweb.e-transactions.fr/cgi/MYframepagepaiement_ip.cgi',
                'https://tpeweb1.e-transactions.fr/cgi/MYframepagepaiement_ip.cgi',
            ),
        ),
        'direct' => array(
            'test' => array(
                'https://preprod-ppps.e-transactions.fr/PPPS.php'
            ),
            'production' => array(
                'https://ppps.e-transactions.fr/PPPS.php',
                'https://ppps1.e-transactions.fr/PPPS.php',
            ),
        ),
        'resabo' => array(
            'test' => array(
                'https://preprod-tpeweb.e-transactions.fr/cgi-bin/ResAbon.cgi'
            ),
            'production' => array(
                'https://tpeweb.e-transactions.fr/cgi-bin/ResAbon.cgi',
                'https://tpeweb1.e-transactions.fr/cgi-bin/ResAbon.cgi',
            ),
        ),
    );

    public function __call($name, $args) {
        if (preg_match('#^get(.)(.*)$#', $name, $matches)) {
            $prop = strtolower($matches[1]) . $matches[2];
            if (isset($this->_configCache[$prop])) {
                return $this->_configCache[$prop];
            } else if (isset($this->_configMapping[$prop])) {
                $key = 'etep/' . $this->_configMapping[$prop];
                $value = $this->_getConfigValue($key);
                $this->_configCache[$prop] = $value;
                return $value;
            }
        } else if (preg_match('#^is(.)(.*)$#', $name, $matches)) {
            $prop = strtolower($matches[1]) . $matches[2];
            if (isset($this->_configCache[$prop])) {
                return $this->_configCache[$prop] == 1;
            } else if (isset($this->_configMapping[$prop])) {
                $key = 'etep/' . $this->_configMapping[$prop];
                $value = $this->_getConfigValue($key);
                $this->_configCache[$prop] = $value;
                return $value == 1;
            }
        }
        throw new Exception('No function ' . $name);
    }

    public function getStore() {
        if (is_null($this->_store)) {
            $this->_store = Mage::app()->getStore();
        }
        return $this->_store;
    }

    public function setStore($storeId = null) {
        if (is_null($storeId)) {
            $this->_store = Mage::app()->getStore();
        } else {
            $this->_store = Mage::getModel('core/store')->load($storeId);
        }
        return $this->_store;
    }

    private function _getConfigValue($name) {
        return Mage::getStoreConfig($name, $this->getStore());
    }

    protected function _getUrls($type, $environment = null) {
        if (is_null($environment)) {
            $environment = $this->getEnvironment();
        }
        $environment = strtolower($environment);
        if (isset($this->_urls[$type][$environment])) {
            return $this->_urls[$type][$environment];
        }
        return array();
    }

    public function getHmacKey() {
        $value = $this->_getConfigValue('etep/merchant/hmackey');
        return Mage::helper('etep/encrypt')->decrypt($value);
    }

    public function getPassword() {
        $value = $this->_getConfigValue('etep/merchant/password');
        return Mage::helper('etep/encrypt')->decrypt($value);
    }

    public function getPasswordplus() {
        $value = $this->_getConfigValue('etep/merchant/passwordplus');
        return Mage::helper('etep/encrypt')->decrypt($value);
    }

    public function getSystemUrls($environment = null) {
        return $this->_getUrls('system', $environment);
    }

    public function getResponsiveUrls($environment = null) {
        return $this->_getUrls('responsive', $environment);
    }

    public function getKwixoUrls($environment = null) {
        return $this->_getUrls('kwixo', $environment);
    }

    public function getAncvUrls($environment = null) {
        return $this->_getUrls('ancv', $environment);
    }

    public function getMobileUrls($environment = null) {
        return $this->_getUrls('mobile', $environment);
    }

    public function getDirectUrls($environment = null) {
        return $this->_getUrls('direct', $environment);
    }

    public function getDefaultNewOrderStatus() {
        return $this->_getConfigValue('etep/defaultoption/new_order_status');
    }

    public function getDefaultCapturedStatus() {
        return $this->_getConfigValue('etep/defaultoption/payment_captured_status');
    }

    public function getDefaultAuthorizedStatus() {
        return $this->_getConfigValue('etep/defaultoption/payment_authorized_status');
    }

    public function getAutomaticInvoice() {
        $value = $this->_getConfigValue('etep/automatic_invoice');
        if (is_null($value)) {
            $value = 0;
        }
        return (int) $value;
    }

    public function getCronStatus() {
        return $this->_getConfigValue('etep/cron_status');
    }

    public function getCronTime() {
        return $this->_getConfigValue('etep/cron_time');
    }
    
    public function getCurrencyConfig() {
        $value = $this->_getConfigValue('etep/info/currency');
        if (is_null($value)) {
            $value = 1;
        }
        return (int) $value;
    }

    public function getResponsiveConfig() {
        $value = $this->_getConfigValue('etep/info/responsive');
        if (is_null($value)) {
            $value = 0;
        }
        return (int) $value;
    }
    
    public function getResAboUrls($environment = null) {
        return $this->_getUrls('resabo', $environment);
    }

    public function getShowInfoToCustomer() {
        $value = $this->_getConfigValue('etep/info_to_customer');
        if (is_null($value)) {
            $value = 1;
        }
        return (int) $value;
    }

    public function getKwixoDefaultCategory() {
        $value = $this->_getConfigValue('etep/kwixo/default_category');
        if (is_null($value)) {
            $value = 1;
        }
        return (int) $value;
    }

    public function getKwixoDefaultCarrierType() {
        $value = $this->_getConfigValue('etep/kwixo/default_carrier_type');
        if (is_null($value)) {
            $value = 4;
        }
        return (int) $value;
    }

    public function getKwixoDefaultCarrierSpeed() {
        $value = $this->_getConfigValue('etep/kwixo/default_carrier_speed');
        if (is_null($value)) {
            $value = 2;
        }
        return (int) $value;
    }

    public function isCronCancelIsActive() {
        return ($this->getCronStatus() == 1) ? true : false;
    }

}
