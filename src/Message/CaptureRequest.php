<?php

namespace Omnipay\Pagarme\Message;

use Omnipay\Pagarme\Helper;
use PagarmeCoreApiLib\Controllers\ChargesController;
use PagarmeCoreApiLib\Models\CreateChargeRequest;

/**
 * Pagarme Capture Request
 *
 * Use this request to capture and process a previously created authorization.
 *
 * Example -- note this example assumes that the authorization has been successful
 * and that the authorization ID returned from the authorization is held in $auth_id.
 * See AuthorizeRequest for the first part of this example transaction:
 *
 * <code>
 *   // Once the transaction has been authorized, we can capture it for final payment.
 *   $transaction = $gateway->capture();
 *   $transaction->setTransactionReference($auth_id);
 *   $response = $transaction->send();
 * </code>
 *
 * @see Omnipay\Pagarme\Message\AuthorizeRequest
 *
 * @method \Omnipay\Pagarme\Message\ChargeResponse send()
 */
class CaptureRequest extends AbstractRequest
{
    /**
     * @return array
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData(): array
    {
        $this->validate('transactionReference');

        $data = [];

        if ($sAmount = $this->getAmountInteger()) {
            $data['amount'] = $sAmount;
        }

        if ($sCode = $this->getCode()) {
            $data['code'] = $sCode;
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return \Omnipay\Pagarme\Message\ChargeResponse
     * @throws \PagarmeCoreApiLib\APIException
     */
    public function sendData($data): ChargeResponse
    {
        $obChargesController = ChargesController::getInstance();
        $obChargesRequest = Helper::arrayToParams(new CreateChargeRequest(), $data);

        /** @var \PagarmeCoreApiLib\Models\GetChargeResponse $obResponse */
        $obResponse = $obChargesController->captureCharge($this->getTransactionReference(), $obChargesRequest);

        return new ChargeResponse($this, $obResponse->jsonSerialize());
    }
}
