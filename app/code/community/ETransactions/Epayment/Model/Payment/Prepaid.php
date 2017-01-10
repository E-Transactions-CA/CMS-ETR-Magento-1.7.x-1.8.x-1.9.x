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

class ETransactions_Epayment_Model_Payment_Prepaid extends ETransactions_Epayment_Model_Payment_Abstract {
	protected $_code = 'etep_prepaid';
	protected $_hasCctypes = true;
}