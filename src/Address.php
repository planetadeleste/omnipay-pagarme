<?php

namespace Omnipay\Pagarme;

use Omnipay\Common\Helper;
use Omnipay\Common\ParametersTrait;
use Omnipay\Pagarme\Exception\InvalidAddressException;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @method array getParameters() = [
 *      "line_1" => "375, Av. General Justo, Centro",
 *      "line_2" => "8º andar",
 *      "zip_code" => "20021130",
 *      "city" => "Rio de Janeiro",
 *      "state" => "RJ",
 *      "country" => "BR"
 *   ]
 */
class Address
{
    use ParametersTrait;

    /**
     * Create a new Address object using the specified parameters
     *
     * @param array|null $parameters An array of parameters to set on the new object
     */
    public function __construct(array $parameters = null)
    {
        $this->initialize($parameters);
    }

    /**
     * Initialize the object with parameters.
     *
     * If any unknown parameters passed, they will be ignored.
     *
     * @param array|null $parameters An associative array of parameters
     *
     * @return $this
     */
    public function initialize(array $parameters = null): self
    {
        $this->parameters = new ParameterBag;
        Helper::initialize($this, $parameters);

        return $this;
    }

    /**
     * @param \Omnipay\Pagarme\CreditCard $obCard
     *
     * @return static
     */
    public static function createFromCard(CreditCard $obCard): self
    {
        $arAddressData = [
            'street'   => $obCard->getBillingAddress1(),
            'line_1'   => $obCard->getBillingAddress1(),
            'line_2'   => $obCard->getBillingAddress2(),
            'zip_code' => $obCard->getBillingPostcode(),
            'city'     => $obCard->getBillingCity(),
            'state'    => $obCard->getBillingState(),
            'country'  => $obCard->getBillingCountry(),
        ];

        return new static($arAddressData);
    }

    /**
     * @return void
     * @throws \Omnipay\Pagarme\Exception\InvalidAddressException
     */
    public function validate()
    {
        $arRequired = ['line_1', 'zip_code', 'city', 'state', 'country'];
        foreach ($arRequired as $skey) {
            if (!$this->getParameter($skey)) {
                throw new InvalidAddressException("The {$skey} parameter is required");
            }
        }
    }

    /**
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setLine_1($sValue): self
    {
        return $this->setParameter('line_1', $sValue);
    }

    /**
     * @return string|null
     */
    public function getLine_1(): ?string
    {
        return $this->getParameter('line_1');
    }

    /**
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setLine_2($sValue): self
    {
        return $this->setParameter('line_2', $sValue);
    }

    /**
     * @return string|null
     */
    public function getLine_2(): ?string
    {
        return $this->getParameter('line_2');
    }

    /**
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setZipCode($sValue): self
    {
        return $this->setParameter('zip_code', $sValue);
    }

    /**
     * @return string|null
     */
    public function getZipCode(): ?string
    {
        return $this->getParameter('zip_code');
    }

    /**
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setCity($sValue): self
    {
        return $this->setParameter('city', $sValue);
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->getParameter('city');
    }

    /**
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setState($sValue): self
    {
        return $this->setParameter('state', $sValue);
    }

    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->getParameter('state');
    }

    /**
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setCountry($sValue): self
    {
        return $this->setParameter('country', $sValue);
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->getParameter('country');
    }

    /**
     * @param array $sValue
     *
     * @return $this
     */
    public function setMetadata(array $sValue): self
    {
        return $this->setParameter('metadata', $sValue);
    }

    /**
     * @return array|null
     */
    public function getMetadata(): ?array
    {
        return $this->getParameter('metadata');
    }

    /**
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setNeighborhood($sValue): self
    {
        return $this->setParameter('neighborhood', $sValue);
    }

    /**
     * @return string|null
     */
    public function getNeighborhood(): ?string
    {
        return $this->getParameter('neighborhood');
    }

    /**
     * @param mixed $sValue
     * @return $this
     */
    public function setStreet($sValue): self
    {
        return $this->setParameter('street', $sValue);
    }

    /**
     * @return string|null
     */
    public function getStreet():? string
    {
        return $this->getParameter('street');
    }
}