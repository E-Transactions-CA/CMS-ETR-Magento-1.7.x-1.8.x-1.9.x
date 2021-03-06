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

class ETransactions_Epayment_Adminhtml_EtepController extends Mage_Adminhtml_Controller_Action {
    /**
     * Fired when an administrator click the total payment on etransactions box
     * @return type
     */
    public function invoiceAction() {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($orderId);
        $data = $this->getRequest()->getParams();

        $payment = $order->getPayment();
        $method = $payment->getMethodInstance();

        $result = $method->makeCapture($order);

        if (!$result) {
            Mage::getSingleton('adminhtml/session')->setCommentText($this->__('Unable to create an invoice.'));
        }

        $this->_redirect('*/sales_order/view', array('order_id' => $orderId));
    }
    
    public function recurringAction() {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($orderId);

        $payment = $order->getPayment();
        $method = $payment->getMethodInstance();

        $result = $method->deleteRecurringPayment($order);

        if (!$result) {
            Mage::getSingleton('adminhtml/session')->setCommentText($this->__('Unable to cancel recurring payment.'));
        }

        $this->_redirect('*/sales_order/view', array('order_id' => $orderId));
    }
}
