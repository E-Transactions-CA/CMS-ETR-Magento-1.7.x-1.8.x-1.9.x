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

class ETransactions_Epayment_Block_Checkout_Payment extends Mage_Payment_Block_Form_Cc {
	protected function _construct() {
		parent::_construct();
		$this->setTemplate('etep/checkout-payment.phtml');
	}

	protected function _prepareLayout() {
		$head = $this->getLayout()->getBlock('head');
		if (!empty($head)) {
			$head->addCss('css/etep/styles.css');
		}
		return parent::_prepareLayout();
	}

	public function getCreditCards() {
		$result = array();
		$cards = $this->getMethod()->getCards();
		$selected = explode(',', $this->getMethod()->getConfigData('cctypes'));
		foreach ($cards as $code => $card) {
			if (in_array($code, $selected)) {
				$result[$code] = $card;
			}
		}
		return $result;
	}

	public function getMethodLabelAfterHtml() {
		$cards = $this->getCreditCards();
		$html = array();
		foreach ($cards as $card) {
			$url = $this->htmlEscape($this->getSkinUrl($card['image']));
			$alt = $this->htmlEscape($card['label']);
			$html[] = '<img class="etep-payment-logo" src="'.$url.'" alt="'.$alt.'"/>';
		}
		$html = '<span class="etep-payment-label">'.implode('&nbsp;', $html).'</span>';
		return $html;
	}
}
