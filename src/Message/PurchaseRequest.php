<?php

namespace Omnipay\Pagarme\Message;

/**
 * Pagarme Purchase Request
 *
 * To charge a credit card or generate a boleto you create a new transaction
 * object. If your API key is in test mode, the supplied card won't actually
 * be charged, though everything else will occur as if in live mode.
 *
 * Either a card object or card_id is required by default. Otherwise,
 * you must provide a card_hash, like the ones returned by Pagarme.js
 * or use the boleto's payment method.
 *
 * Example:
 *
 * <code>
 *   $obGateway = \Omnipay\Omnipay::create('Pagarme');
 *   $obGateway->setApiKey('...');
 *   $sCustomerId = 'cus_...';
 *
 *   $obAuth = $obGateway->authorize();
 *   $obAuth->setCustomerReference($sCustomerId);
 *   $obAuth->setCode('ORD_TEST_'.date('Ymd'));
 *   $obAuth->setItems([
 *      [
 *          'price'       => 2990,
 *          'quantity'    => 3,
 *          'description' => 'Chaveiro do Tesseract',
 *          'category'    => 'Chaveiros',
 *          'code'        => 'PRD_TEST_001'
 *      ]
 *   ]);
 *   $obAuth->setAmount(2990 * 3);
 *
 *   $obCard = new \Omnipay\Pagarme\CreditCard();
 *   $obCard->setName('Tony Stark');
 *   $obCard->setNumber('4000000000000010');
 *   $obCard->setExpiryMonth(1);
 *   $obCard->setExpiryYear(23);
 *   $obCard->setCvv('1234');
 *   $obCard->setAddress1('10880, Malibu Point, Malibu Central');
 *   $obCard->setPostcode('90265');
 *   $obCard->setCity('Malibu');
 *   $obCard->setState('CA');
 *   $obCard->setCountry('US');
 *
 *   $obAuth->setPaymentMethod('credit_card');
 *   $obAuth->setCard($obCard);
 *   $obAuth->setInstallments(3);
 *   $obAuth->setStatementDescriptor('AVENGERS');
 *
 *   $obResponse = $obAuth->send();
 * </code>
 *
 * Because a purchase request in Pagarme looks similar to an
 * Authorize request, this class simply extends the AuthorizeRequest
 * class and over-rides the getData method setting capture = true.
 *
 * @see  \Omnipay\Pagarme\Gateway
 * @link https://docs.pagar.me/api/?shell#criando-uma-transao
 */
class PurchaseRequest extends AuthorizeRequest
{
    /**
     * @return array|mixed
     * @throws \Omnipay\Common\Exception\InvalidCreditCardException
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData()
    {
        $data = parent::getData();
        $data['capture'] = 'true';
        return $data;
    }
}
