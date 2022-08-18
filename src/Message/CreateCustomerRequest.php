<?php
/**
 * Pagarme Create Customer Request
 */

namespace Omnipay\Pagarme\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Pagarme\Helper;
use PagarmeCoreApiLib\Controllers\CustomersController;
use PagarmeCoreApiLib\Models\CreateAddressRequest;
use PagarmeCoreApiLib\Models\CreateCustomerRequest as CreateCustomerApiRequest;
use PagarmeCoreApiLib\Models\CreatePhoneRequest;
use PagarmeCoreApiLib\Models\GetCustomerResponse;

/**
 * Pagarme Create Customer Request
 *
 * Customer objects allow you to perform recurring charges and
 * track multiple charges that are associated with the same customer.
 * The API allows you to create, delete, and update your customers.
 * You can retrieve individual customers as well as a list of all of
 * your customers.
 *
 * Either a pair name|email or document_number (valid CPF or CNPJ) is required.
 *
 * Example:
 *
 * <code>
 *   // Create a gateway for the Pagarme Gateway
 *   // (routes to GatewayFactory::create)
 *   $gateway = Omnipay::create('Pagarme');
 *
 *   // Initialise the gateway
 *   $gateway->setApiKey("sk_test_....");
 *
 *   $obCustomer = $obGateway->createCustomer();
 *   $obCustomer->setCustomer([
 *      "name"          => "Tony Stark",
 *      "email"         => "tonystarkk@avengers.com",
 *      "code"          => "MY_CUSTOMER_001",
 *      "document"      => "93095135270",
 *      "type"          => "individual",
 *      "document_type" => "CPF",
 *      "gender"        => "male",
 *      "birthdate"     => "05/03/1984",
 *   ]);
 *   $obCustomer->setAddress([
 *      "line_1"   => "375, Av. General Justo, Centro",
 *      "line_2"   => "8º andar",
 *      "zip_code" => "20021130",
 *      "city"     => "Rio de Janeiro",
 *      "state"    => "RJ",
 *      "country"  => "BR"
 *   ]);
 *   $obCustomer->setHomePhone([
 *      "country_code" => "55",
 *      "area_code"    => "21",
 *      "number"       => "000000000"
 *   ]);
 *
 *   $obResponse = $obCustomer->send();
 *
 *   if ($obResponse->isSuccessful()) {
 *       echo "Gateway createCustomer was successful.\n";
 *       // Find the customer ID
 *       $customer_id = $obResponse->id;
 *       echo "Customer ID = " . $customer_id . "\n";
 *   }
 * </code>
 *
 * @link https://docs.pagar.me/reference#criar-cliente-1
 *
 * @method CreateCustomerResponse send()
 */
class CreateCustomerRequest extends AbstractRequest
{
    /**
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setName($sValue): self
    {
        return $this->setParameter('name', $sValue);
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getParameter('name');
    }

    /**
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setEmail($sValue): self
    {
        return $this->setParameter('email', $sValue);
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->getParameter('email');
    }

    /**
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setDocument($sValue): self
    {
        return $this->setParameter('document', $sValue);
    }

    /**
     * @return string|null
     */
    public function getDocument(): ?string
    {
        return $this->getParameter('document');
    }

    /**
     * Set document type. Values: PASSPORT , CPF, CNPJ
     *
     * @param mixed $sValue
     *
     * @return $this
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function setDocumentType($sValue): self
    {
        $sValue = strtoupper($sValue);
        $arValid = ['PASSPORT', 'CPF', 'CNPJ'];
        if (!in_array($sValue, $arValid)) {
            $sMessage = sprintf("Invalid document type %s. Must be one of %s", $sValue, join(", ", $arValid));
            throw new InvalidRequestException($sMessage);
        }

        return $this->setParameter('document_type', $sValue);
    }

    /**
     * @return string
     */
    public function getDocumentType(): string
    {
        return $this->getParameter('document_type');
    }

    /**
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setType($sValue): self
    {
        return $this->setParameter('type', $sValue);
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->getParameter('type');
    }


    /**
     * @return array
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws \Omnipay\Pagarme\Exception\InvalidAddressException
     */
    public function getData(): array
    {
        $data = [];

        if ($arCustomer = $this->getCustomer()) {
            Helper::initialize($this, $arCustomer);
            $data = array_merge($data, $arCustomer);
            $this->setCard($arCustomer);
        }

        if ($obAddress = $this->getAddress()) {
            $obAddress->validate();
            $data['address'] = $obAddress->getParameters();
        }

        if ($arHomePhone = $this->getHomePhone()) {
            $data['phones'] = ['home_phone' => $arHomePhone];
        }

        if ($arMobilePhone = $this->getMobilePhone()) {
            if (isset($data['phones'])) {
                $data['phones']['mobile_phone'] = $arMobilePhone;
            } else {
                $data['phones'] = ['mobile_phone' => $arMobilePhone];
            }
        }

        // Validate Required Attributes
        $this->validate('document', 'name', 'email', 'document_type');

        return $data;
    }

    /**
     * @param array $data
     *
     * @return \Omnipay\Pagarme\Message\CreateCustomerResponse
     * @throws \PagarmeCoreApiLib\APIException
     */
    public function sendData($data): CreateCustomerResponse
    {
        $obCustomer = CustomersController::getInstance();
        $obCustomerRequest = new CreateCustomerApiRequest();

        // Set address
        if (($arAddress = Helper::arrayGet($data, 'address')) && is_array($arAddress)) {
            $obAddress = new CreateAddressRequest();
            Helper::arrayToParams($obAddress, $arAddress);
            $data['address'] = $obAddress;
        }

        // Set phones
        if (($arPhones = Helper::arrayGet($data, 'phones')) && is_array($arPhones)) {
            foreach ($arPhones as $sPhone => $arPhoneData) {
                $obPhones = new CreatePhoneRequest();
                Helper::arrayToParams($obPhones, $arPhoneData);
                $data['phones'][$sPhone] = $obPhones;
            }
        }

        // Set params
        Helper::arrayToParams($obCustomerRequest, $data);

        /** @var GetCustomerResponse $obResponse */
        $obResponse = $obCustomer->createCustomer($obCustomerRequest);

        return new CreateCustomerResponse($this, $obResponse->jsonSerialize());
    }

    public function getEndpoint(): string
    {
        return $this->endpoint.'customers';
    }
}
