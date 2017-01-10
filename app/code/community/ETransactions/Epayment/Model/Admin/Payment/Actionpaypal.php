<?php

class ETransactions_Epayment_Model_Admin_Payment_Actionpaypal extends ETransactions_Epayment_Model_Admin_Payment_Action {
	public function toOptionArray() {
		$options = parent::toOptionArray();
		array_splice($options, 1, 1);
		return $options;
	}
}