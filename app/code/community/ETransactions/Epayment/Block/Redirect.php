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
class ETransactions_Epayment_Block_Redirect extends Mage_Page_Block_Html {

    public function getFormFields() {
        $order = Mage::registry('etep/order');
        $payment = $order->getPayment()->getMethodInstance();
        $cntr = Mage::getSingleton('etep/etransactions');
        $values = $cntr->buildSystemParams($order, $payment);
        $cntr->logDebug(sprintf('Values: %s', json_encode($values)));
        return $values;
    }

    public function getInputType() {
        $config = Mage::getSingleton('etep/config');
        if ($config->isDebug()) {
            return 'text';
        }
        return 'hidden';
    }

    public function getKwixoUrl() {
        $etransactions = Mage::getSingleton('etep/etransactions');
        $urls = $etransactions->getConfig()->getKwixoUrls();
        return $etransactions->checkUrls($urls);
    }

    public function getMobileUrl() {
        $etransactions = Mage::getSingleton('etep/etransactions');
        $urls = $etransactions->getConfig()->getMobileUrls();
        return $etransactions->checkUrls($urls);
    }

    public function getSystemUrl() {
        $etransactions = Mage::getSingleton('etep/etransactions');
        $urls = $etransactions->getConfig()->getSystemUrls();
        return $etransactions->checkUrls($urls);
    }

}
