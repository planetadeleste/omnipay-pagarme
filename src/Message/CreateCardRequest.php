<?php
/**
 * Pagarme Create Credit Card Request
 *
 */
namespace Omnipay\Pagarme\Message;

use Omnipay\Pagarme\Address;
use Omnipay\Pagarme\Helper;
use PagarmeCoreApiLib\Controllers\CustomersController;
use PagarmeCoreApiLib\Models\CreateAddressRequest;
use PagarmeCoreApiLib\Models\CreateCardRequest as CreateCardApiRequest;

/**
 * Pagarme Create Credit Card Request
 *
 * Whenever you make a request through the Pagarme's API the
 * cardholder information is stored, so that in future,
 * you can use this information to new charges, or
 * implementing features like one-click-buy.
 *
 * Either a card object or card_id is required. Otherwise,
 * you must provide a card_hash, like the ones returned by Pagarme.js.
 *
 * The customer_id is optional.
 *
 * <code>
 *   // Create a credit card object
 *   // This card can be used for testing.
 *   $new_card = new CreditCard(array(
 *               'firstName'    => 'Example',
 *               'lastName'     => 'Customer',
 *               'number'       => '5555555555554444',
 *               'expiryMonth'  => '01',
 *               'expiryYear'   => '2020',
 *               'cvv'          => '456',
 *   ));
 *
 *   // Do a create card transaction on the gateway
 *   $response = $gateway->createCard(array(
 *       'card'              => $new_card,
 *       'customerReference' => $customer_id,
 *   ))->send();
 *   if ($response->isSuccessful()) {
 *       echo "Gateway createCard was successful.\n";
 *       // Find the card ID
 *       $card_id = $response->getCardReference();
 *       echo "Card ID = " . $card_id . "\n";
 *   }
 * </code>
 *
 * @link https://docs.pagar.me/api/?shell#cartes
 *
 * @method CreateCardResponse send()
 */
class CreateCardRequest extends AbstractRequest
{
    /**
     * @return array
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws \Omnipay\Common\Exception\InvalidCreditCardException
     */
    public function getData(): array
    {
        $data = [];

        $this->validate('customerReference');

        if ($obCard = $this->getCard()) {
            $data = $this->getCardData();
            if (!$obCard->getBillingAddressId() && $obCard->getBillingAddress1()) {
                $data['billing_address'] = Address::createFromCard($obCard)->getParameters();
            }

        } elseif ($this->getCardHash()) {
            $data['token'] = $this->getCardHash();
        } else {
            $this->validate('card_number');
        }

        return $data;
    }

    /**
     * @param $data
     *
     * @return \Omnipay\Pagarme\Message\CreateCardResponse
     * @throws \PagarmeCoreApiLib\APIException
     */
    public function sendData($data): CreateCardResponse
    {
        $obCustomer = CustomersController::getInstance();
        $obCardRequest = new CreateCardApiRequest();

        if ($arBillingAddress = Helper::arrayGet($data, 'billing_address', null, 'is_array')) {
            $obAddress = new CreateAddressRequest();
            Helper::arrayToParams($obAddress, $arBillingAddress);
            $obCardRequest->billingAddress = $obAddress;
        }

        /** @var \PagarmeCoreApiLib\Models\GetCardResponse $obResponse */
        $obResponse = $obCustomer->createCard($this->getCustomerReference(), $obCardRequest);
        return new CreateCardResponse($this, $obResponse->jsonSerialize());
    }
    
    public function getEndpoint(): string
    {
        return $this->endpoint . 'cards';
    }
}
