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

class ETransactions_Epayment_Model_Admin_Backend_Encrypted extends Mage_Core_Model_Config_Data
{
    protected function _afterLoad()
    {
        $value = $this->getValue();
        $value = empty($value) ? false : Mage::helper('etep/encrypt')->decrypt($value);
        $this->setValue($value);
    }

    protected function _beforeSave()
    {
        $value = $this->getValue();
        $value = Mage::helper('etep/encrypt')->encrypt($value);
        $this->setValue($value);
    }
}
