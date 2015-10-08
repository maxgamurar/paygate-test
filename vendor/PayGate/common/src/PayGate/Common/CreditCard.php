<?php
/**
 * Credit Card class
 */

namespace PayGate\Common;

use DateTime;
use DateTimeZone;
use PayGate\Common\Exception\InvalidCreditCardException;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Credit Card class
 *
 */
class CreditCard
{
    const BRAND_VISA = 'visa';
    const BRAND_MASTERCARD = 'mastercard';
    const BRAND_DISCOVER = 'discover';
    const BRAND_AMEX = 'amex';
    const BRAND_DINERS_CLUB = 'diners_club';
    const BRAND_JCB = 'jcb';
    const BRAND_SWITCH = 'switch';
    const BRAND_SOLO = 'solo';
    const BRAND_DANKORT = 'dankort';
    const BRAND_MAESTRO = 'maestro';
    const BRAND_FORBRUGSFORENINGEN = 'forbrugsforeningen';
    const BRAND_LASER = 'laser';

    /**
     * Internal storage
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $parameters;

    /**
     * Create a new CreditCard object
     *
     * @param array $parameters An array of parameters to set on the new object
     */
    public function __construct($parameters = null)
    {
        $this->initialize($parameters);
    }

    /**
     * All card brands
     *
     * @return array
     */
    public function getSupportedBrands()
    {
        return array(
            static::BRAND_VISA => '/^4\d{12}(\d{3})?$/',
            static::BRAND_MASTERCARD => '/^(5[1-5]\d{4}|677189)\d{10}$/',
            static::BRAND_DISCOVER => '/^(6011|65\d{2}|64[4-9]\d)\d{12}|(62\d{14})$/',
            static::BRAND_AMEX => '/^3[47]\d{13}$/',
            static::BRAND_DINERS_CLUB => '/^3(0[0-5]|[68]\d)\d{11}$/',
            static::BRAND_JCB => '/^35(28|29|[3-8]\d)\d{12}$/',
            static::BRAND_SWITCH => '/^6759\d{12}(\d{2,3})?$/',
            static::BRAND_SOLO => '/^6767\d{12}(\d{2,3})?$/',
            static::BRAND_DANKORT => '/^5019\d{12}$/',
            static::BRAND_MAESTRO => '/^(5[06-8]|6\d)\d{10,17}$/',
            static::BRAND_FORBRUGSFORENINGEN => '/^600722\d{10}$/',
            static::BRAND_LASER => '/^(6304|6706|6709|6771(?!89))\d{8}(\d{4}|\d{6,7})?$/',
        );
    }

    /**
     * Initialize the object with parameters.
     *
     * @param array $parameters associative array of parameters
     * @return CreditCard interface.
     */
    public function initialize($parameters = null)
    {
        $this->parameters = new ParameterBag;

        Helper::initialize($this, $parameters);

        return $this;
    }

    /**
     * Get all parameters.
     *
     * @return array An associative array of parameters.
     */
    public function getParameters()
    {
        return $this->parameters->all();
    }

    /**
     * Get one parameter.
     *
     * @return mixed A single parameter value.
     */
    protected function getParameter($key)
    {
        return $this->parameters->get($key);
    }

    /**
     * Set one parameter.
     *
     * @param string $key Parameter key
     * @param mixed $value Parameter value
     * @return CreditCard interface.
     */
    protected function setParameter($key, $value)
    {
        $this->parameters->set($key, $value);

        return $this;
    }

    /**
     * Set the credit card year.
     *
     * @param string $key Parameter key
     * @param mixed $value Parameter value
     * @return CreditCard interface.
     */
    protected function setYearParameter($key, $value)
    {
        if (null === $value || '' === $value) {
            $value = null;
        } else {
            $value = (int) gmdate('Y', gmmktime(0, 0, 0, 1, 1, (int) $value));
        }

        return $this->setParameter($key, $value);
    }

    /**
     * Validate this credit card.
     *
     * @return void
     */
    public function validate()
    {
        foreach (array('number', 'expiryMonth', 'expiryYear') as $key) {
            if (!$this->getParameter($key)) {
                throw new InvalidCreditCardException("The $key parameter is required");
            }
        }

        if ($this->getExpiryDate('Ym') < gmdate('Ym')) {
            throw new InvalidCreditCardException('Card has expired');
        }

        if (!Helper::validateLuhn($this->getNumber())) {
            throw new InvalidCreditCardException('Card number is invalid');
        }

        if (!is_null($this->getNumber()) && !preg_match('/^\d{12,19}$/i', $this->getNumber())) {
            throw new InvalidCreditCardException('Card number should have 12 to 19 digits');
        }
    }

    /**
     * Get Card Title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getBillingTitle();
    }

    /**
     * Set Card Title.
     *
     * @param string $value Parameter value
     * @return CreditCard interface.
     */
    public function setTitle($value)
    {
        $this->setBillingTitle($value);
        $this->setShippingTitle($value);

        return $this;
    }

    /**
     * Get Card First Name.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->getBillingFirstName();
    }

    /**
     * Set Card First Name (Billing and Shipping).
     *
     * @param string $value Parameter value
     * @return CreditCard interface.
     */
    public function setFirstName($value)
    {
        $this->setBillingFirstName($value);
        $this->setShippingFirstName($value);

        return $this;
    }

    /**
     * Get Card Last Name.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->getBillingLastName();
    }

    /**
     * Set Card Last Name (Billing and Shipping).
     *
     * @param string $value Parameter value
     * @return CreditCard interface.
     */
    public function setLastName($value)
    {
        $this->setBillingLastName($value);
        $this->setShippingLastName($value);

        return $this;
    }

    /**
     * Get Card Name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getBillingName();
    }

    /**
     * Set Card Name (Billing and Shipping).
     *
     * @param string $value Parameter value
     * @return CreditCard interface.
     */
    public function setName($value)
    {
        $this->setBillingName($value);
        $this->setShippingName($value);

        return $this;
    }

    /**
     * Get Card Number.
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->getParameter('number');
    }

    /**
     * Set Card Number
     *
     * @param string $value Parameter value
     * @return CreditCard interface.
     */
    public function setNumber($value)
    {        
        return $this->setParameter('number', preg_replace('/\D/', '', $value));
    }

    /**
     * Get the last 4 digits of the card number.
     *
     * @return string
     */
    public function getNumberLastFour()
    {
        return substr($this->getNumber(), -4, 4) ?: null;
    }

    /**
     * Returns a masked credit card number with only the last 4 chars visible
     *
     * @param string $mask Character to use in place of numbers
     * @return string
     */
    public function getNumberMasked($mask = 'X')
    {
        $maskLength = strlen($this->getNumber()) - 4;

        return str_repeat($mask, $maskLength) . $this->getNumberLastFour();
    }

    /**
     * Credit Card Brand
     *
     * Iterates to determine the brand of this card
     *
     * @return string
     */
    public function getBrand()
    {
        foreach ($this->getSupportedBrands() as $brand => $val) {
            if (preg_match($val, $this->getNumber())) {
                return $brand;
            }
        }
    }

    /**
     * Get the card expiry month.
     *
     * @return string
     */
    public function getExpiryMonth()
    {
        return $this->getParameter('expiryMonth');
    }

    /**
     * Sets the card expiry month.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setExpiryMonth($value)
    {
        return $this->setParameter('expiryMonth', (int) $value);
    }

    /**
     * Get the card expiry year.
     *
     * @return string
     */
    public function getExpiryYear()
    {
        return $this->getParameter('expiryYear');
    }

    /**
     * Sets the card expiry year.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setExpiryYear($value)
    {
        return $this->setYearParameter('expiryYear', $value);
    }

    /**
     * Get the card expiry date, using the specified date format string.
     *
     * @param string $format
     *
     * @return string
     */
    public function getExpiryDate($format)
    {
        return gmdate($format, gmmktime(0, 0, 0, $this->getExpiryMonth(), 1, $this->getExpiryYear()));
    }

    /**
     * Get the card start month.
     *
     * @return string
     */
    public function getStartMonth()
    {
        return $this->getParameter('startMonth');
    }

    /**
     * Sets the card start month.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setStartMonth($value)
    {
        return $this->setParameter('startMonth', (int) $value);
    }

    /**
     * Get the card start year.
     *
     * @return string
     */
    public function getStartYear()
    {
        return $this->getParameter('startYear');
    }

    /**
     * Sets the card start year.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setStartYear($value)
    {
        return $this->setYearParameter('startYear', $value);
    }

    /**
     * Get the card start date, using the specified date format string
     *
     * @param string $format
     *
     * @return string
     */
    public function getStartDate($format)
    {
        return gmdate($format, gmmktime(0, 0, 0, $this->getStartMonth(), 1, $this->getStartYear()));
    }

    /**
     * Get the card CVV.
     *
     * @return string
     */
    public function getCvv()
    {
        return $this->getParameter('cvv');
    }

    /**
     * Sets the card CVV.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setCvv($value)
    {
        return $this->setParameter('cvv', $value);
    }

    /**
     * Get the card issue number.
     *
     * @return string
     */
    public function getIssueNumber()
    {
        return $this->getParameter('issueNumber');
    }

    /**
     * Sets the card issue number.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setIssueNumber($value)
    {
        return $this->setParameter('issueNumber', $value);
    }

    /**
     * Get the card billing title.
     *
     * @return string
     */
    public function getBillingTitle()
    {
        return $this->getParameter('billingTitle');
    }

    /**
     * Sets the card billing title.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setBillingTitle($value)
    {
        return $this->setParameter('billingTitle', $value);
    }

    /**
     * Get the card billing name.
     *
     * @return string
     */
    public function getBillingName()
    {
        return trim($this->getBillingFirstName() . ' ' . $this->getBillingLastName());
    }

    /**
     * Sets the card billing name.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setBillingName($value)
    {
        $names = explode(' ', $value, 2);
        $this->setBillingFirstName($names[0]);
        $this->setBillingLastName(isset($names[1]) ? $names[1] : null);

        return $this;
    }

    /**
     * Get the first part of the card billing name.
     *
     * @return string
     */
    public function getBillingFirstName()
    {
        return $this->getParameter('billingFirstName');
    }

    /**
     * Sets the first part of the card billing name.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setBillingFirstName($value)
    {
        return $this->setParameter('billingFirstName', $value);
    }

    /**
     * Get the last part of the card billing name.
     *
     * @return string
     */
    public function getBillingLastName()
    {
        return $this->getParameter('billingLastName');
    }

    /**
     * Sets the last part of the card billing name.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setBillingLastName($value)
    {
        return $this->setParameter('billingLastName', $value);
    }

    /**
     * Get the billing company name.
     *
     * @return string
     */
    public function getBillingCompany()
    {
        return $this->getParameter('billingCompany');
    }

    /**
     * Sets the billing company name.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setBillingCompany($value)
    {
        return $this->setParameter('billingCompany', $value);
    }

    /**
     * Get the billing address, line 1.
     *
     * @return string
     */
    public function getBillingAddress1()
    {
        return $this->getParameter('billingAddress1');
    }

    /**
     * Sets the billing address, line 1.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setBillingAddress1($value)
    {
        return $this->setParameter('billingAddress1', $value);
    }

    /**
     * Get the billing address, line 2.
     *
     * @return string
     */
    public function getBillingAddress2()
    {
        return $this->getParameter('billingAddress2');
    }

    /**
     * Sets the billing address, line 2.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setBillingAddress2($value)
    {
        return $this->setParameter('billingAddress2', $value);
    }

    /**
     * Get the billing city.
     *
     * @return string
     */
    public function getBillingCity()
    {
        return $this->getParameter('billingCity');
    }

    /**
     * Sets billing city.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setBillingCity($value)
    {
        return $this->setParameter('billingCity', $value);
    }

    /**
     * Get the billing postcode.
     *
     * @return string
     */
    public function getBillingPostcode()
    {
        return $this->getParameter('billingPostcode');
    }

    /**
     * Sets the billing postcode.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setBillingPostcode($value)
    {
        return $this->setParameter('billingPostcode', $value);
    }

    /**
     * Get the billing state.
     *
     * @return string
     */
    public function getBillingState()
    {
        return $this->getParameter('billingState');
    }

    /**
     * Sets the billing state.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setBillingState($value)
    {
        return $this->setParameter('billingState', $value);
    }

    /**
     * Get the billing country name.
     *
     * @return string
     */
    public function getBillingCountry()
    {
        return $this->getParameter('billingCountry');
    }

    /**
     * Sets the billing country name.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setBillingCountry($value)
    {
        return $this->setParameter('billingCountry', $value);
    }

    /**
     * Get the billing phone number.
     *
     * @return string
     */
    public function getBillingPhone()
    {
        return $this->getParameter('billingPhone');
    }

    /**
     * Sets the billing phone number.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setBillingPhone($value)
    {
        return $this->setParameter('billingPhone', $value);
    }

    /**
     * Get the billing fax number.
     *
     * @return string
     */
    public function getBillingFax()
    {
        return $this->getParameter('billingFax');
    }

    /**
     * Sets the billing fax number.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setBillingFax($value)
    {
        return $this->setParameter('billingFax', $value);
    }

    /**
     * Get the title of the card shipping name.
     *
     * @return string
     */
    public function getShippingTitle()
    {
        return $this->getParameter('shippingTitle');
    }

    /**
     * Sets the title of the card shipping name.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setShippingTitle($value)
    {
        return $this->setParameter('shippingTitle', $value);
    }

    /**
     * Get the card shipping name.
     *
     * @return string
     */
    public function getShippingName()
    {
        return trim($this->getShippingFirstName() . ' ' . $this->getShippingLastName());
    }

    /**
     * Sets the card shipping name.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setShippingName($value)
    {
        $names = explode(' ', $value, 2);
        $this->setShippingFirstName($names[0]);
        $this->setShippingLastName(isset($names[1]) ? $names[1] : null);

        return $this;
    }

    /**
     * Get the first part of the card shipping name.
     *
     * @return string
     */
    public function getShippingFirstName()
    {
        return $this->getParameter('shippingFirstName');
    }

    /**
     * Sets the first part of the card shipping name.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setShippingFirstName($value)
    {
        return $this->setParameter('shippingFirstName', $value);
    }

    /**
     * Get the last part of the card shipping name.
     *
     * @return string
     */
    public function getShippingLastName()
    {
        return $this->getParameter('shippingLastName');
    }

    /**
     * Sets the last part of the card shipping name.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setShippingLastName($value)
    {
        return $this->setParameter('shippingLastName', $value);
    }

    /**
     * Get the shipping company name.
     *
     * @return string
     */
    public function getShippingCompany()
    {
        return $this->getParameter('shippingCompany');
    }

    /**
     * Sets the shipping company name.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setShippingCompany($value)
    {
        return $this->setParameter('shippingCompany', $value);
    }

    /**
     * Get the shipping address, line 1.
     *
     * @return string
     */
    public function getShippingAddress1()
    {
        return $this->getParameter('shippingAddress1');
    }

    /**
     * Sets the shipping address, line 1.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setShippingAddress1($value)
    {
        return $this->setParameter('shippingAddress1', $value);
    }

    /**
     * Get the shipping address, line 2.
     *
     * @return string
     */
    public function getShippingAddress2()
    {
        return $this->getParameter('shippingAddress2');
    }

    /**
     * Sets the shipping address, line 2.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setShippingAddress2($value)
    {
        return $this->setParameter('shippingAddress2', $value);
    }

    /**
     * Get the shipping city.
     *
     * @return string
     */
    public function getShippingCity()
    {
        return $this->getParameter('shippingCity');
    }

    /**
     * Sets the shipping city.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setShippingCity($value)
    {
        return $this->setParameter('shippingCity', $value);
    }

    /**
     * Get the shipping postcode.
     *
     * @return string
     */
    public function getShippingPostcode()
    {
        return $this->getParameter('shippingPostcode');
    }

    /**
     * Sets the shipping postcode.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setShippingPostcode($value)
    {
        return $this->setParameter('shippingPostcode', $value);
    }

    /**
     * Get the shipping state.
     *
     * @return string
     */
    public function getShippingState()
    {
        return $this->getParameter('shippingState');
    }

    /**
     * Sets the shipping state.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setShippingState($value)
    {
        return $this->setParameter('shippingState', $value);
    }

    /**
     * Get the shipping country.
     *
     * @return string
     */
    public function getShippingCountry()
    {
        return $this->getParameter('shippingCountry');
    }

    /**
     * Sets the shipping country.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setShippingCountry($value)
    {
        return $this->setParameter('shippingCountry', $value);
    }

    /**
     * Get the shipping phone number.
     *
     * @return string
     */
    public function getShippingPhone()
    {
        return $this->getParameter('shippingPhone');
    }

    /**
     * Sets the shipping phone number.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setShippingPhone($value)
    {
        return $this->setParameter('shippingPhone', $value);
    }

    /**
     * Get the shipping fax number.
     *
     * @return string
     */
    public function getShippingFax()
    {
        return $this->getParameter('shippingFax');
    }

    /**
     * Sets the shipping fax number.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setShippingFax($value)
    {
        return $this->setParameter('shippingFax', $value);
    }

    /**
     * Get the billing address, line 1.
     *
     * @return string
     */
    public function getAddress1()
    {
        return $this->getParameter('billingAddress1');
    }

    /**
     * Sets the billing and shipping address, line 1.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setAddress1($value)
    {
        $this->setParameter('billingAddress1', $value);
        $this->setParameter('shippingAddress1', $value);

        return $this;
    }

    /**
     * Get the billing address, line 2.
     *
     * @return string
     */
    public function getAddress2()
    {
        return $this->getParameter('billingAddress2');
    }

    /**
     * Sets the billing and shipping address, line 2.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setAddress2($value)
    {
        $this->setParameter('billingAddress2', $value);
        $this->setParameter('shippingAddress2', $value);

        return $this;
    }

    /**
     * Get the billing city.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->getParameter('billingCity');
    }

    /**
     * Sets the billing and shipping city.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setCity($value)
    {
        $this->setParameter('billingCity', $value);
        $this->setParameter('shippingCity', $value);

        return $this;
    }

    /**
     * Get the billing postcode.
     *
     * @return string
     */
    public function getPostcode()
    {
        return $this->getParameter('billingPostcode');
    }

    /**
     * Sets the billing and shipping postcode.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setPostcode($value)
    {
        $this->setParameter('billingPostcode', $value);
        $this->setParameter('shippingPostcode', $value);

        return $this;
    }

    /**
     * Get the billing state.
     *
     * @return string
     */
    public function getState()
    {
        return $this->getParameter('billingState');
    }

    /**
     * Sets the billing and shipping state.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setState($value)
    {
        $this->setParameter('billingState', $value);
        $this->setParameter('shippingState', $value);

        return $this;
    }

    /**
     * Get the billing country.
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->getParameter('billingCountry');
    }

    /**
     * Sets the billing and shipping country.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setCountry($value)
    {
        $this->setParameter('billingCountry', $value);
        $this->setParameter('shippingCountry', $value);

        return $this;
    }

    /**
     * Get the billing phone number.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->getParameter('billingPhone');
    }

    /**
     * Sets the billing and shipping phone number.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setPhone($value)
    {
        $this->setParameter('billingPhone', $value);
        $this->setParameter('shippingPhone', $value);

        return $this;
    }

    /**
     * Get the billing fax number..
     *
     * @return string
     */
    public function getFax()
    {
        return $this->getParameter('billingFax');
    }

    /**
     * Sets the billing and shipping fax number.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setFax($value)
    {
        $this->setParameter('billingFax', $value);
        $this->setParameter('shippingFax', $value);

        return $this;
    }

    /**
     * Get the card billing company name.
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->getParameter('billingCompany');
    }

    /**
     * Sets the billing and shipping company name.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setCompany($value)
    {
        $this->setParameter('billingCompany', $value);
        $this->setParameter('shippingCompany', $value);

        return $this;
    }

    /**
     * Get the Card Holder's email address.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->getParameter('email');
    }

    /**
     * Sets the Card Holder's email address.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setEmail($value)
    {
        return $this->setParameter('email', $value);
    }

    /**
     * Get the Card Holder's birthday.
     *
     * @return string
     */
    public function getBirthday($format = 'Y-m-d')
    {
        $value = $this->getParameter('birthday');

        return $value ? $value->format($format) : null;
    }

    /**
     * Sets the Card Holder's birthday.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setBirthday($value)
    {
        if ($value) {
            $value = new DateTime($value, new DateTimeZone('UTC'));
        } else {
            $value = null;
        }

        return $this->setParameter('birthday', $value);
    }

    /**
     * Get the Card Holder's gender.
     *
     * @return string
     */
    public function getGender()
    {
        return $this->getParameter('gender');
    }

    /**
     * Sets the Card Holder's gender.
     *
     * @param string $value
     * @return CreditCard interface.
     */
    public function setGender($value)
    {
        return $this->setParameter('gender', $value);
    }
}
