<?php

namespace Omnipay\Pagarme;

use Omnipay\Common\AbstractGateway;
use Omnipay\Pagarme\Message\AuthorizeRequest;
use Omnipay\Pagarme\Message\CaptureRequest;
use Omnipay\Pagarme\Message\CreateCardRequest;
use Omnipay\Pagarme\Message\CreateCustomerRequest;
use Omnipay\Pagarme\Message\CreateHookRequest;
use Omnipay\Pagarme\Message\FetchCustomerCardRequest;
use Omnipay\Pagarme\Message\FetchHookRequest;
use Omnipay\Pagarme\Message\ListCustomerCardsRequest;
use Omnipay\Pagarme\Message\ListHookRequest;
use Omnipay\Pagarme\Message\PurchaseRequest;
use PagarmeCoreApiLib\Configuration;

/**
 * Pagarme Gateway
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
 *               'address1'     => 'Street name, Street number, Neighborhood',
 *               'address2'     => 'address complementary',
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
 * Test modes:
 *
 * Pagarme accounts have test-mode API keys as well as live-mode
 * API keys. Data created with test-mode credentials will never
 * hit the credit card networks and will never cost anyone money.
 *
 * Unlike some gateways, there is no test mode endpoint separate
 * to the live mode endpoint, the Pagarme API endpoint is the same
 * for test and for live.
 *
 * Setting the testMode flag on this gateway has no effect.  To
 * use test mode just use your test mode API key.
 *
 * Authentication:
 *
 * Authentication is by means of a single secret API key set as
 * the apiKey parameter when creating the gateway object.
 *
 * @see  \Omnipay\Common\AbstractGateway
 * @see  \Omnipay\Pagarme\Message\AbstractRequest
 * @link https://docs.pagar.me/
 *
 * @method AuthorizeRequest         authorize(array $options = [])
 * @method PurchaseRequest          purchase(array $options = [])
 * @method CaptureRequest           capture(array $options = [])
 * @method CreateCardRequest        createCard(array $options = [])
 * @method CreateCustomerRequest    createCustomer(array $options = [])
 * @method CreateHookRequest        createHook(array $options = [])
 * @method FetchHookRequest         fetchHook(array $options = [])
 * @method ListHookRequest          listHook(array $options = [])
 * @method ListCustomerCardsRequest listCustomerCards(array $options = [])
 * @method FetchCustomerCardRequest fetchCustomerCard(array $options = [])
 */
class Gateway extends AbstractGateway
{
    public function getName(): string
    {
        return 'Pagarme';
    }

    /**
     * Get the gateway parameters
     *
     * @return array
     */
    public function getDefaultParameters(): array
    {
        return [
            'apiKey' => '',
        ];
    }

    /**
     * Set the gateway API Key
     *
     * Authentication is by means of a single secret API key set as
     * the apiKey parameter when creating the gateway object.
     *
     * Pagarme accounts have test-mode API keys as well as live-mode
     * API keys. Data created with test-mode credentials will never
     * hit the credit card networks and will never cost anyone money.
     *
     * Unlike some gateways, there is no test mode endpoint separate
     * to the live mode endpoint, the Stripe API endpoint is the same
     * for test and for live.
     *
     * Setting the testMode flag on this gateway has no effect.  To
     * use test mode just use your test mode API key.
     *
     * @param  string  $value
     *
     * @return Gateway provides a fluent interface.
     */
    public function setApiKey(string $value): Gateway
    {
        $this->setParameter('apiKey', $value);
        $this->setAuth();

        return $this;
    }

    public function setAuth(): void
    {
        if (!$this->getApiKey()) {
            return;
        }

        Configuration::$basicAuthPassword = '';
        Configuration::$basicAuthUserName = $this->getApiKey();
    }

    /**
     * Get the gateway API Key
     *
     * Authentication is by means of a single secret API key set as
     * the apiKey parameter when creating the gateway object.
     *
     * @return string
     */
    public function getApiKey(): ?string
    {
        return $this->getParameter('apiKey');
    }

    /**
     * @param  string  $name
     * @param  array   $arguments
     *
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function __call(string $name, array $arguments = [])
    {
        $sMethod = ucfirst($name).'Request';
        $sClass = 'Omnipay\\Pagarme\\Message\\'.$sMethod;
        $arOptions = empty($arguments) ? [] : $arguments[0];
        if (!is_array($arOptions)) {
            $arOptions = [$arOptions];
        }

        if ($this->getApiKey()) {
            $arOptions['apiKey'] = $this->getApiKey();
        }

        $obCreateRequest = $this->createRequest($sClass, $arOptions);
        $obCreateRequest->initialize($arOptions);

        return $obCreateRequest;
    }
}
