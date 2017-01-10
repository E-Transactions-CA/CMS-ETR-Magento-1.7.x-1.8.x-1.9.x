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

class ETransactions_Epayment_Model_Admin_Subscription {
	public function toOptionArray() {
		return array(
			array('value' => 'access', 'label' => Mage::helper('etep')->__('E-transactions Access')),
			
			array('value' => 'premium', 'label' => Mage::helper('etep')->__('E-transactions Premium')),
		);
	}
}