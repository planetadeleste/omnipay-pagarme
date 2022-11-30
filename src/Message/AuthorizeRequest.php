<?php
/**
 * Pagarme Authorize Request
 */

namespace Omnipay\Pagarme\Message;

use DeviceDetector\DeviceDetector;
use Omnipay\Pagarme\Helper;
use Omnipay\Pagarme\Traits\ShippingTrait;
use PagarmeCoreApiLib\APIException;
use PagarmeCoreApiLib\Controllers\OrdersController;
use PagarmeCoreApiLib\Models\CreateDeviceRequest;
use PagarmeCoreApiLib\Models\CreateOrderItemRequest;
use PagarmeCoreApiLib\Models\CreateOrderRequest;

/**
 * Pagarme Authorize Request
 *
 * An Authorize request is similar to a purchase request but the
 * charge issues an authorization (or pre-authorization), and no money
 * is transferred.  The transaction will need to be captured later
 * in order to effect payment. Uncaptured transactions expire in 5 days.
 *
 * Either a card object or card_id is required by default. Otherwise,
 * you must provide a card_hash, like the ones returned by Pagarme.js
 * or use the boleto's payment method.
 *
 * Pagarme gateway supports only two types of "payment_method":
 *
 * * credit_card
 * * boleto
 *
 *
 * Optionally, you can provide the customer details to use the antifraude
 * feature. These details is passed using the following attributes available
 * on credit card object:
 *
 * * firstName
 * * lastName
 * * name
 * * birthday
 * * gender
 * * address1 (must be in the format "street, street_number and neighborhood")
 * * address2 (used to specify the optional parameter "street_complementary")
 * * postcode
 * * phone (must be in the format "DDD PhoneNumber" e.g. "19 98888 5555")
 * * holder_document_number (CPF or CNPJ)
 *
 * Example:
 *
 * <code>
 *   // Create a gateway for the Pagarme Gateway
 *   // (routes to GatewayFactory::create)
 *   $gateway = Omnipay::create('Pagarme');
 *
 *   // Initialise the gateway
 *   $gateway->initialize(array(
 *       'apiKey' => 'MyApiKey',
 *   ));
 *
 *   // Create a credit card object
 *   // This card can be used for testing.
 *   $card = new CreditCard(array(
 *               'firstName'    => 'Example',
 *               'lastName'     => 'Customer',
 *               'number'       => '4242424242424242',
 *               'expiryMonth'  => '01',
 *               'expiryYear'   => '2020',
 *               'cvv'          => '123',
 *               'email'        => 'customer@example.com',
 *               'address1'     => 'Street name, Street number, Complementary',
 *               'address2'     => 'Neighborhood',
 *               'postcode'     => '05443100',
 *               'phone'        => '19 3242 8855',
 *               'holder_document_number' => '214.278.589-40',
 *   ));
 *
 *   // Do an authorize transaction on the gateway
 *   $transaction = $gateway->authorize(array(
 *       'amount'           => '10.00',
 *       'soft_descriptor'  => 'test',
 *       'payment_method'   => 'credit_card',
 *       'card'             => $card,
 *       'metadata'         => array(
 *                                 'product_id' => 'ID1111',
 *                                 'invoice_id' => 'IV2222',
 *                             ),
 *   ));
 *   $response = $transaction->send();
 *   if ($response->isSuccessful()) {
 *       echo "Authorize transaction was successful!\n";
 *       $sale_id = $response->getTransactionReference();
 *       $customer_id = $response->getCustomerReference();
 *       $card_id = $response->getCardReference();
 *       echo "Transaction reference = " . $sale_id . "\n";
 *   }
 * </code>
 *
 * @see  https://docs.pagar.me/capturing-card-data/
 * @see  \Omnipay\Pagarme\Gateway
 * @see  \Omnipay\Pagarme\Message\CaptureRequest
 * @link https://docs.pagar.me/api/?shell#objeto-transaction
 *
 * @method \Omnipay\Pagarme\Message\OrderResponse send()
 */
class AuthorizeRequest extends AbstractRequest
{
    use ShippingTrait;

    /**
     * @return array|mixed
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws \Omnipay\Common\Exception\InvalidCreditCardException
     */
    public function getData()
    {
        $this->validate('amount', 'paymentMethod');

        $data = [];

        $data['code'] = $this->getCode();
        $data['amount'] = $this->getAmountInteger();
        $data['antifraud_enabled'] = $this->getAntifraudEnabled();
        $data['metadata'] = $this->getMetadata();
        $data['closed'] = $this->getClosed();
        $data['ip'] = $this->getClientIp();
        $data['currency'] = 'BRL';


        if ($iCustomerId = $this->getCustomerReference()) {
            $data['customer_id'] = $iCustomerId;
        } else {
            $data['customer'] = $this->getCustomer();
        }

        // Set device
        if (!$sPlatform = $this->getDevice()) {
            $obDd = new DeviceDetector($_SERVER['HTTP_USER_AGENT']);
            $sPlatform = $obDd->getDeviceName();
        }
        if (!empty($sPlatform)) {
            $data['device'] = ['platform' => $sPlatform];
        }

        // Add Items
        if (($arItems = $this->getItems()) && $arItems->count()) {
            $data['items'] = $arItems->getData();
        }

        // Add payment method
        $sPaymentMethod = strtolower($this->getPaymentMethod());
        $arPayment = [
            'payment_method' => $sPaymentMethod,
            $sPaymentMethod  => $this->getPaymentMethodData($sPaymentMethod)
        ];
        $data['payments'] = [$arPayment];

        // Add shipping
        if ($this->getRecipientName() && $this->getShippingType() && $this->getShippingAddress()) {
            $data['shipping'] = $this->getShippingData();
        }

        return $data;
    }

    /**
     * @param string $sPaymentMethod
     *
     * @return array
     * @throws \Omnipay\Common\Exception\InvalidCreditCardException
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    protected function getPaymentMethodData(string $sPaymentMethod): array
    {
        $arPaymentMethod = [];

        switch ($sPaymentMethod) {
            case 'boleto':
                $arPaymentMethod = [
                    'bank'            => $this->getIssuer(),
                    'instructions'    => $this->getInstructions(),
                    'due_at'          => $this->getDueAt(),
                    'nosso_numero'    => $this->getNossoNumero(),
                    'type'            => $this->getType(),
                    'document_number' => $this->getDocumentNumber()
                ];
                break;

            case 'credit_card':
                $arPaymentMethod = [
                    'installments'         => $this->getInstallments(),
                    'statement_descriptor' => $this->getStatementDescriptor(),
                    'operation_type'       => $this->getOperationType() ?? 'auth_only',
                ];
                if ($sCardId = $this->getCardId()) {
                    $arPaymentMethod['card_id'] = $sCardId;
                } elseif ($sCartToken = $this->getCardToken()) {
                    $arPaymentMethod['card_token'] = $sCartToken;
                } else {
                    $arPaymentMethod['card'] = $this->getCardData();
                }
                break;

            case 'bank_transfer':
                $arPaymentMethod['bank'] = $this->getIssuer();
                break;

            case 'pix':
                if ($sExpireIn = $this->getExpiresIn()) {
                    $arPaymentMethod['expires_in'] = $sExpireIn;
                } elseif ($sExpireAt = $this->getExpiresAt()) {
                    $arPaymentMethod['expires_at'] = $sExpireAt;
                }

                if ($arAdditionalData = $this->getAdditionalInformation()) {
                    $arPaymentMethod['additional_information'] = $arAdditionalData;
                }
                break;
        }

        return $arPaymentMethod;
    }

    /**
     * @return bool
     */
    public function getClosed(): bool
    {
        return (bool)$this->getParameter('closed');
    }

    /**
     * @return string|null
     */
    public function getDevice(): ?string
    {
        return $this->getParameter('device');
    }

    /**
     * @param $data
     *
     * @return \Omnipay\Pagarme\Message\OrderResponse
     * @throws \PagarmeCoreApiLib\APIException
     */
    public function sendData($data): Response
    {
//        $data = array_filter($data);

        try {

            $obOrdersController = OrdersController::getInstance();

            // Set Items
            $arItems = [];
            foreach ($data['items'] as $arItem) {
                $obItem = new CreateOrderItemRequest();
                Helper::arrayToParams($obItem, $arItem);
                $arItems[] = $obItem;
            }
            Helper::arraySet($data, 'items', $arItems);

            // Set Payments
            $arPayments = [];
            foreach ($data['payments'] as $arPayment) {
                $arPayments[] = Helper::toPaymentRequest($arPayment);
            }
            Helper::arraySet($data, 'payments', $arPayments);

            // Set Device
            if ($arDevice = Helper::arrayGet($data, 'device', null, 'is_array')) {
                $obDeviceRequest = Helper::arrayToParams(new CreateDeviceRequest(), $arDevice);
                Helper::arraySet($data, 'device', $obDeviceRequest);
            }

            // Set Shipping
            if ($arShipping = Helper::arrayGet($data, 'shipping', null, 'is_array')) {
                $obShippingRequest = Helper::toShippingRequest($arShipping);
                Helper::arraySet($data, 'shipping', $obShippingRequest);
            }

            /** @var CreateOrderRequest $obOrderRequest */
            /** @var \PagarmeCoreApiLib\Models\GetOrderResponse $obResponse */

            $obOrderRequest = Helper::arrayToParams(new CreateOrderRequest(), $data);
            $obResponse = $obOrdersController->createOrder($obOrderRequest);

            return new OrderResponse($this, $obResponse->jsonSerialize());
        } catch (APIException $ex) {
            $sResponseBody = $ex->getContext()->getResponse()->getRawBody();
            $arResponse = json_decode($sResponseBody, true);

            return new OrderResponse($this, $arResponse);
        }
    }

    public function getEndpoint(): string
    {
        return $this->endpoint.'orders';
    }

    /**
     * @param bool $sValue
     *
     * @return $this
     */
    public function setClosed(bool $sValue): self
    {
        return $this->setParameter('closed', $sValue);
    }

    /**
     * @param string $sValue
     *
     * @return $this
     */
    public function setDevice(string $sValue): self
    {
        return $this->setParameter('device', $sValue);
    }

}
