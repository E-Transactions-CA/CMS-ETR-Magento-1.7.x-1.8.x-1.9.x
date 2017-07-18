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

class ETransactions_Epayment_Model_Observer_AdminCreateOrder extends Mage_Core_Model_Observer
{
	private static $_oldOrder = null;

	public function onBeforeCreate($observer) {
		$event = $observer->getEvent();
		$session = $event->getSession();

        if ($session->getOrder()->getId()) {
			ETransactions_Epayment_Model_Observer_AdminCreateOrder::$_oldOrder = $session->getOrder();
		}
	}

	public function onAfterSubmit($observer) {
		$oldOrder = ETransactions_Epayment_Model_Observer_AdminCreateOrder::$_oldOrder;
		if (!is_null($oldOrder)) {
			$order = $observer->getEvent()->getOrder();
			if (!is_null($order)) {
				$payment = $order->getPayment();
				$oldPayment = $oldOrder->getPayment();

				// Payment information
				$payment->setEtepAction($oldPayment->getEtepAction());
				$payment->setEtepAuthorization($oldPayment->getEtepAuthorization());
				$payment->setEtepCapture($oldPayment->getEtepCapture());
				$payment->setEtepFirstPayment($oldPayment->getEtepFirstPayment());
				$payment->setEtepSecondPayment($oldPayment->getEtepSecondPayment());
				$payment->setEtepSecondThird($oldPayment->getEtepSecondPThird());
				$payment->setEtepDelay($oldPayment->getEtepDelay());
				$payment->setEtepSecondPayment($oldPayment->getEtepSecondPayment());

				// Transactions
				$oldTxns = Mage::getModel('sales/order_payment_transaction')->getCollection();
				$oldTxns->addFilter('payment_id', $oldPayment->getId());
				foreach ($oldTxns as $oldTxn) {
					$payment->setTransactionId($oldTxn->getTxnId());
					$payment->setParentTransactionId($oldTxn->getParentTxnId());
					$txn = $payment->addTransaction($oldTxn->getTxnType());
					$txn->setParentTxnId($oldTxn->getParentTxnId());
					$txn->setIsClosed($oldTxn->getIsClosed());
					$infos = $oldTxn->getAdditionalInformation();
					foreach ($infos as $key => $value) {
						$txn->setAdditionalInformation($key, $value);
					}

					$txn->setOrderPaymentObject($payment);
					$txn->setPaymentId($payment->getId());
					$txn->setOrderId($order->getId());
					$txn->save();
				}

				$payment->save();
			}
        }
	}
}