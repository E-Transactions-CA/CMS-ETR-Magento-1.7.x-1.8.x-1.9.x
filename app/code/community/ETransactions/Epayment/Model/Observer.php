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
class ETransactions_Epayment_Model_Observer extends Mage_Core_Model_Observer {

    /**
     * ajoute un bloc à la fin du bloc "content"
     * 
     * utilise l'événement "controller_action_layout_load_before"
     * 
     * @param Varien_Event_Observer $observer
     * @return \ETransactions_Epayment_Model_Observer
     */
    public function addBlockAtEndOfMainContent(Varien_Event_Observer $observer) {
        $event = $observer->getEvent();
        $data = $event->getData();
        $section = $data['action']->getRequest()->getParam('section', false);
        if ($section == 'etep') {
            $layout = $observer->getEvent()->getLayout()->getUpdate();
            $layout->addHandle('etep_pres');
        }
        return $this;
    }

    public function logDebug($message) {
        Mage::log($message, Zend_Log::DEBUG, 'etransactions-epayment.log');
    }

    public function logWarning($message) {
        Mage::log($message, Zend_Log::WARN, 'etransactions-epayment.log');
    }

    public function logError($message) {
        Mage::log($message, Zend_Log::ERR, 'etransactions-epayment.log');
    }

    public function logFatal($message) {
        Mage::log($message, Zend_Log::ALERT, 'etransactions-epayment.log');
    }

    public function onAfterOrderSave($observer) {
        // Find the order
        $order = $observer->getEvent()->getOrder();
        if (empty($order)) {
            return $this;
        }

        // This order must be paid by E-Transactions
        $payment = $order->getPayment();
        if (empty($payment)) {
            return $this;
        }
        $method = $payment->getMethodInstance();
        if (!($method instanceof ETransactions_Epayment_Model_Payment_Abstract)) {
            return $this;
        }

        // E-Transactions Direct must be activated
        $config = $method->getEtransactionsConfig();
        if ($config->getSubscription() != ETransactions_Epayment_Model_Config::SUBSCRIPTION_OFFER2 && $config->getSubscription() != ETransactions_Epayment_Model_Config::SUBSCRIPTION_OFFER3) {
            return $this;
        }

        // Action must be "Manual"
        if ($payment->getEtepAction() != ETransactions_Epayment_Model_Payment_Abstract::ETRANSACTION_MANUAL) {
            return $this;
        }

        // No capture must be prevously done
        $capture = $payment->getEtepCapture();
        if (!empty($capture)) {
            return $this;
        }

        // Order must be "invoiceable"
        if (!$order->canInvoice()) {
            return $this;
        }

        // Auto capture status must be defined
        $captureStatus = $method->getConfigAutoCaptureStatus();
        if (empty($captureStatus)) {
            return $this;
        }

        // Order status must match auto capture status
        $orderStatus = $order->getStatus();
        if ($orderStatus != $captureStatus) {
            return $this;
        }

        $this->logDebug(sprintf('Order %s: Automatic capture', $order->getIncrementId()));

        $result = false;
        $error = 'Unknown error';
        try {
            $result = $method->makeCapture($order);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        if (!$result) {
            $message = 'Automatic E-Transactions payment capture failed: %s.';
            $message = $method->__($message, $error);
            $this->logDebug(sprintf('Order %s: Automatic capture - %s', $order->getIncrementId(), $message));
            $status = $order->addStatusHistoryComment($message);
            $status->save();
        }

        return $this;
    }

    public function cancelTask($observer) {
        $config = Mage::getSingleton('etep/config');
        $now = strtotime("-" . $config->getCronTime() . " minutes");
        $now = date('Y-m-d H:i:s', $now);

        $orders = Mage::getModel('sales/order')->getCollection()
                ->join('order_payment', '`order_payment`.parent_id = `main_table`.entity_id')
                ->addFieldToSelect('entity_id', 'orderId')
                ->addFieldToFilter('`order_payment`.method', array('like' => "etep\_%"))
                ->addFieldToFilter('`main_table`.status', 'pending')
                ->addFieldToFilter('updated_at', array('lt' => $now));
//        var_dump($orders->getSelect()->__toString());
//        die();
        $count = 0;
        foreach ($orders as $order) {
            $orderModel = Mage::getModel('sales/order')->load($order->getData('orderId'));
            try {
                $message = sprintf('Payment was canceled by E-Transactions Cron: %s', $orderModel->getIncrementId());
                $orderModel->cancel();
                $history = $orderModel->addStatusHistoryComment($message);
                $history->setIsCustomerNotified(false);
                $orderModel->save();
                $this->logDebug($message);
                $count++;
            } catch (Exception $e) {
                $message = $e->getMessage() . ' : ' . $orderModel->getIncrementId();
                $this->logFatal($message);
                Mage::logException($message);
            }
        }

        return 'Orders canceled : ' . $count;
        die();
    }

    public function __($message) {
        $helper = Mage::helper('etep');
        $args = func_get_args();
        return call_user_func_array(array($helper, '__'), $args);
    }

}
