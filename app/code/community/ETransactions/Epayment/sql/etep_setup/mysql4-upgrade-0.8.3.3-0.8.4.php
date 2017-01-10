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

$code = 'fianet_category';

$catalogEav = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');
$attrId = $catalogEav->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'fianet_category');
if (empty($attrId)) {
	$def = array(
		'input' => 'select',
		'group' => 'General',
		'label' => 'FIA-NET Category',
		'required' => false,
		'scope' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
		'source' => 'etep/admin_fianet_categories',
		'type' => 'int',
		'visible' => true,
		'visible_on_front' => false,
	);
	$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'fianet_category', $def);
}

$attrId = $catalogEav->getAttributeId(Mage_Catalog_Model_Category::ENTITY, 'fianet_category');
if (empty($attrId)) {
	$def = array(
		'input' => 'select',
		'group' => 'FIA-NET',
		'label' => 'Default category for products',
		'required' => false,
		'scope' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
		'source' => 'etep/admin_fianet_categories',
		'type' => 'int',
		'visible' => true,
		'visible_on_front' => false,
	);
	$installer->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'fianet_category', $def);
}

$attrId = $catalogEav->getAttributeId(Mage_Catalog_Model_Category::ENTITY, 'fianet_apply_to_subs');
if (empty($attrId)) {
	$def = array(
		'inputl' => 'select',
		'group' => 'FIA-NET',
		'label' => 'Apply to sub-categories',
		'required' => false,
		'scope' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
		'source' => 'eav/entity_attribute_source_boolean',
		'type' => 'int',
		'visible' => true,
		'visible_on_front' => false,
	);
	$installer->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'fianet_apply_to_subs', $def);
}

// Finalization
$installer->endSetup();