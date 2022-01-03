<?php

namespace Omnipay\Pagarme\Message;

use Omnipay\Pagarme\ItemBag;
use Omnipay\Pagarme\Traits\BoletoPaymentTrait;
use Omnipay\Pagarme\Traits\CardPaymentTrait;
use Omnipay\Pagarme\Traits\CashPaymentTrait;
use Omnipay\Pagarme\Traits\PixPaymentTrait;
use Omnipay\Pagarme\Address;
use Omnipay\Pagarme\CreditCard;
use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;
use PagarmeCoreApiLib\Configuration;

/**
 * Abstract Request
 *
 * @method Response send()
 * @method ItemBag  getItems()
 */
abstract class AbstractRequest extends BaseAbstractRequest
{
    use PixPaymentTrait;
    use CashPaymentTrait;
    use BoletoPaymentTrait;
    use CardPaymentTrait;

    /**
     * Live or Test Endpoint URL
     *
     * @var string URL
     */
    protected $endpoint = 'https://api.pagar.me/core/v5/';

    /**
     * @param array $parameters
     *
     * @return $this|\Omnipay\Pagarme\Message\AbstractRequest
     */
    public function initialize(array $parameters = []): self
    {
        parent::initialize($parameters);

        Configuration::$basicAuthPassword = '';
        Configuration::$basicAuthUserName = $this->getApiKey();

        return $this;
    }

    /**
     * Get API key
     *
     * @return string API key
     */
    public function getApiKey(): ?string
    {
        return $this->getParameter('apiKey');
    }

    /**
     * Sets the card.
     *
     * @param CreditCard|array $value
     *
     * @return $this
     */
    public function setCard($value): self
    {
        if ($value && !$value instanceof CreditCard) {
            $value = new CreditCard($value);
        }

        return $this->setParameter('card', $value);
    }

    /**
     * Set the items in this order
     *
     * @param ItemBag|array $items An array of items in this order
     *
     * @return $this
     */
    public function setItems($items): self
    {
        if ($items && !$items instanceof ItemBag) {
            $items = new ItemBag($items);
        }

        return $this->setParameter('items', $items);
    }

    /**
     * Set API key
     *
     * @param string $value
     *
     * @return AbstractRequest provides a fluent interface.
     */
    public function setApiKey(string $value): AbstractRequest
    {
        return $this->setParameter('apiKey', $value);
    }

    /**
     * Get Customer Data
     *
     * @return array customer data
     */
    public function getCustomer(): array
    {
        return $this->getParameter('customer');
    }

    /**
     * Set Customer data
     *
     * @param array $value
     *
     * @return AbstractRequest provides a fluent interface.
     */
    public function setCustomer(array $value): AbstractRequest
    {
        return $this->setParameter('customer', $value);
    }

    /**
     * Get the customer reference
     *
     * @return string customer reference
     */
    public function getCustomerReference(): string
    {
        return $this->getParameter('customerReference');
    }

    /**
     * Set the customer reference
     *
     * Used when calling CreateCardRequest on an existing customer. If this
     * parameter is not set then a new customer is created.
     *
     * @return AbstractRequest provides a fluent interface.
     */
    public function setCustomerReference($value): AbstractRequest
    {
        return $this->setParameter('customerReference', $value);
    }

    /**
     * Get Metadata
     *
     * @return array|null metadata
     */
    public function getMetadata(): ?array
    {
        return $this->getParameter('metadata');
    }

    /**
     *
     * @param array $value
     *
     * @return $this
     */
    public function setMetadata(array $value): self
    {
        return $this->setParameter('metadata', $value);
    }

    /**
     * Get HTTP Method.
     *
     * This is nearly always POST but can be over-ridden in sub classes.
     *
     * @return string the HTTP method
     */
    public function getHttpMethod(): string
    {
        return 'POST';
    }

    /**
     * @param $data
     *
     * @return \Omnipay\Common\Message\ResponseInterface|\Omnipay\Pagarme\Message\Response
     */
    public function sendData($data)
    {
        $headers = [
            'Authorization' => 'Basic '.base64_encode($this->getApiKey().':'),
            'Content-Type'  => 'application/json'
        ];
        $httpRequest = $this->httpClient->request(
            $this->getHttpMethod(),
            $this->getEndpoint(),
            $headers,
            json_encode($data),
        );
        $payload = json_decode($httpRequest->getBody()->getContents(), true);

        return $this->createResponse($payload);
    }

    /**
     * @param $data
     *
     * @return \Omnipay\Pagarme\Message\Response
     */
    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }

    /**
     * @return array|null
     */
    public function getHomePhone(): ?array
    {
        return $this->getParameter('home_phone');
    }

    /**
     * @param array $sValue
     *
     * @return $this
     */
    public function setMobilePhone(array $sValue): self
    {
        return $this->setParameter('mobile_phone', $sValue);
    }

    /**
     * @return array|null
     */
    public function getMobilePhone(): ?array
    {
        return $this->getParameter('mobile_phone');
    }

    /**
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setCode($sValue): self
    {
        return $this->setParameter('code', $sValue);
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->getParameter('code');
    }

    /**
     * @param bool $sValue
     *
     * @return $this
     */
    public function setAntifraudEnabled(bool $sValue): self
    {
        return $this->setParameter('antifraud_enabled', $sValue);
    }

    /**
     * @return bool
     */
    public function getAntifraudEnabled(): bool
    {
        return (bool)$this->getParameter('antifraud_enabled');
    }

    /**
     * Get Query Options.
     *
     * Must be over-ridden in sub classes that make GET requests
     * with query parameters.
     *
     * @return array The query Options
     */
    protected function getOptions(): array
    {
        return [];
    }

    protected function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Get the card data.
     *
     * Because the pagarme gateway uses a common format for passing
     * card data to the API, this function can be called to get the
     * data from the associated card object in the format that the
     * API requires.
     *
     * @return array card data
     * @throws \Omnipay\Common\Exception\InvalidCreditCardException
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    protected function getCardData(): array
    {
        $card = $this->getCard();
        $card->validate();

        return $card->getData();
    }

    /**
     * Get the card.
     *
     * @return CreditCard
     */
    public function getCard(): ?CreditCard
    {
        return $this->getParameter('card');
    }

    /**
     * Get the Customer data.
     *
     * Because the pagarme gateway uses a common format for passing
     * customer data to the API, this function can be called to get the
     * data from the card object in the format that the API requires.
     *
     * @return array customer data
     */
    protected function getCustomerData(): array
    {
        $card = $this->getCard();
        $data = [];

        $data['customer']['name'] = $card->getName();
        $data['customer']['email'] = $card->getEmail();
        $data['customer']['gender'] = $card->getGender();
        $data['customer']['birthdate'] = $card->getBirthday('m-d-Y');
        $data['customer']['document'] = $card->getHolderDocumentNumber();

        $this->setAddressFromCard();
        if ($this->getAddress() && ($arAddress = $this->getAddress()->getParameters())) {
            $data['customer']['address'] = $arAddress;
        }

        $arrayPhone = $this->extractDddPhone($card->getPhone());
        if (!empty($arrayPhone['area_code'])) {
            $this->setHomePhone($arrayPhone);
            $data['customer']['phones'] = ['home_phone' => $arrayPhone];
        }

        return $data;
    }

    /**
     * @return $this
     */
    protected function setAddressFromCard(): self
    {
        if (!$obCard = $this->getCard()) {
            return $this;
        }

        $arAddress = [
            'line_1'  => $obCard->getAddress1(),
            'line_2'  => $obCard->getAddress2(),
            'city'    => $obCard->getCity(),
            'state'   => $obCard->getState(),
            'country' => $obCard->getCountry(),
        ];

        return $this->setAddress($arAddress);
    }

    /**
     * @param array $sValue
     *
     * @return $this
     */
    public function setAddress(array $sValue): self
    {
        $obAddress = new Address($sValue);
        return $this->setParameter('address', $obAddress);
    }

    /**
     * @return Address|null
     */
    public function getAddress(): ?Address
    {
        return $this->getParameter('address');
    }

    /**
     * Separate DDD from phone numbers in an array
     * containing two keys:
     *
     * * ddd
     * * number
     *
     * @param string|integer $phoneNumber phone number with DDD (byref)
     *
     * @return array the Phone number and the DDD with two digits
     */
    protected function extractDddPhone($phoneNumber): array
    {
        $arrayPhone = [];
        $phone = preg_replace("/[^0-9]/", "", $phoneNumber);
        if (substr($phone, 0, 1) === "0") {
            $arrayPhone['area_code'] = substr($phone, 1, 2);
            $arrayPhone['number'] = substr($phone, 3);
        } elseif (strlen($phone) < 10) {
            $arrayPhone['area_code'] = '';
            $arrayPhone['number'] = $phone;
        } else {
            $arrayPhone['area_code'] = substr($phone, 0, 2);
            $arrayPhone['number'] = substr($phone, 2);
        }

        return $arrayPhone;
    }

    /**
     * @param array $sValue
     *
     * @return $this
     */
    public function setHomePhone(array $sValue): self
    {
        return $this->setParameter('home_phone', $sValue);
    }

    /**
     * Separate data from the credit card Address in an
     * array containing the keys:
     * * street
     * * street_number
     * * complementary
     *
     * It's important to provide the parameter $address
     * with the information in the given order and separated
     * by commas.
     *
     * @param string $address
     *
     * @return array containing the street, street_number and complementary
     */
    protected function extractAddress(string $address): array
    {
        $result = [];
        $explode = array_map('trim', explode(',', $address));

        $result['street'] = $explode[0];
        $result['street_number'] = $explode[1] ?? '';
        $result['complementary'] = $explode[2] ?? '';

        return $result;
    }
}
