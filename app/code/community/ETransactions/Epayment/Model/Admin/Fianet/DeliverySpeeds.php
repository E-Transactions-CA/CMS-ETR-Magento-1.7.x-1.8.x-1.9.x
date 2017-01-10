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

/**
 * List of FIA-NET delivery speed
 *
 * @author olivier
 */
class ETransactions_Epayment_Model_Admin_Fianet_DeliverySpeeds {
    public function toOptionArray() {
        $helper = Mage::helper('etep');
        return array(
            array('value' => null, 'label' => ''),
            array('value' => "1", 'label' => $helper->__('Express (moins de 24h)')),
            array('value' => "2", 'label' => $helper->__('Standard')),
        );
    }
}
