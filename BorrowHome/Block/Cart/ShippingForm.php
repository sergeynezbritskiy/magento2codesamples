<?php

namespace Magecom\BorrowHome\Block\Cart;


/**
 * Class ShippingForm
 *
 * @package Magecom\BorrowHome\Block\Cart
 */
class ShippingForm extends \Magento\Framework\View\Element\Template {
	/**
	 * @var \Magento\Framework\Data\Form\FormKey
	 */
	private $formKey;

	/**
	 * @var \Magento\Directory\Api\CountryInformationAcquirerInterface
	 */
	private $countryInformationAcquirer;

	/**
	 * @var \Magento\Customer\Model\Session
	 */
	private $customerSession;

	/**
	 * ShippingForm constructor.
	 *
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Magento\Framework\Data\Form\FormKey $formKey
	 * @param \Magento\Directory\Api\CountryInformationAcquirerInterface $countryInformationAcquirer
	 * @param \Magento\Customer\Model\Session $customerSession
	 * @param array $data
	 */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\Data\Form\FormKey $formKey,
		\Magento\Directory\Api\CountryInformationAcquirerInterface $countryInformationAcquirer,
		\Magento\Customer\Model\Session $customerSession,
		array $data = []
	) {
		parent::__construct( $context, $data );
		$this->formKey                    = $formKey;
		$this->countryInformationAcquirer = $countryInformationAcquirer;
		$this->customerSession            = $customerSession;
	}

	/**
	 * @return string
	 */
	public function getSubmitFormUrl() {
		return $this->getUrl( 'borrow-home/order/create' );
	}

	/**
	 * @return string
	 */
	public function getFormKey() {
		return $this->formKey->getFormKey();
	}

	public function getCountriesList() {
		//TODO_EO_BE filter available countries only
		$countries = $this->countryInformationAcquirer->getCountriesInfo();
		$data      = [];
		/** @var \Magento\Directory\Model\Data\CountryInformation $country */
		foreach ( $countries as $country ) {
			if ( $country->getFullNameLocale() ) {
				$data[ $country->getTwoLetterAbbreviation() ] = __( $country->getFullNameLocale() );
			}
		}
		asort( $data );

		return $data;
	}

	/**
	 * @return string
	 */
	public function getDefaultCountryCode() {
		//TODO_EO_BE move to config
		return 'NO';
	}

	public function isNeedRegion() {
		$currencyCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();

		return $currencyCode == 'USD';
	}

	/**
	 * @return string
	 */
	public function getCustomerDataJsonConfig() {
		$result = [];
		if ( $this->customerSession->isLoggedIn() ) {
			$customer           = $this->customerSession->getCustomer();
			$address            = $customer->getDefaultShippingAddress() ?: $customer->getDefaultBillingAddress();
			$result['customer'] = $customer->toArray( [ 'firstname', 'lastname', 'email' ] );
			$result['address']  = $address ? $address->toArray() : [];
		}

		return json_encode( $result );
	}
}
