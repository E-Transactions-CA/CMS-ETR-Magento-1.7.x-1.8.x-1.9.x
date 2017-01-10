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

$crypt = Mage::helper('etep/encrypt');

$res = Mage::getSingleton('core/resource');
$cnx = $res->getConnection('core-write');
$table = $res->getTableName('core_config_data');

/**
 * Encrypt existing data
 */
// Find raw values
$query = 'select config_id, value from '.$table.' where path in ("etep/merchant/hmackey", "etep/merchant/password")';
$rows = $cnx->fetchAll($query);

// Process each vlaue
foreach ($rows as $row) {
    $id = $row['config_id'];
    $value = $row['value'];

    // Encrypt the value
    $value = $crypt->encrypt($value);

    // And save to the db
    $cnx->update(
        $table,
        array('value' => $value),
        array('config_id = ?' => $id)
    );
}

/**
 * Add default data as encoded if needed
 */

// HMAC Key
$cfg = new Mage_Core_Model_Config();
$query = 'select 1 from '.$table.' where path = "etep/merchant/hmackey" and scope = "default" and scope_id = 0';
$rows = $cnx->fetchAll($query);
if (empty($rows)) {
	$value = '4642EDBBDFF9790734E673A9974FC9DD4EF40AA2929925C40B3A95170FF5A578E7D2579D6074E28A78BD07D633C0E72A378AD83D4428B0F3741102B69AD1DBB0';
	$value = $crypt->encrypt($value);
	$cfg->saveConfig('etep/merchant/hmackey', $value);

}

// Password
$cfg = new Mage_Core_Model_Config();
$query = 'select 1 from '.$table.' where path = "etep/merchant/password" and scope = "default" and scope_id = 0';
$rows = $cnx->fetchAll($query);
if (empty($rows)) {
	$value = 'ETRANSACTIONS';
	$value = $crypt->encrypt($value);
	$cfg->saveConfig('etep/merchant/password', $value);

}

// Finalization
$installer->endSetup();