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

// Initialization
$installer = $this;
$installer->startSetup();

$catalogEav = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');

$defs = array(
	'etep_action' => array(
		'type' => 'varchar',
	),
	'etep_delay' => array(
		'type' => 'varchar',
	),
	'etep_authorization' => array(
		'type' => 'text',
	),
	'etep_capture' => array(
		'type' => 'text',
	),
	'etep_first_payment' => array(
		'type' => 'text',
	),
	'etep_second_payment' => array(
		'type' => 'text',
	),
	'etep_third_payment' => array(
		'type' => 'text',
	),
);

$entity = 'order_payment';

foreach ($defs as $name => $def) {
	$installer->addAttribute('order_payment', $name, $def);
}

// Finalization
$installer->endSetup();