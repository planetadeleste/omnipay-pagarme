<?php

namespace Omnipay\Pagarme\Message;

use Omnipay\Pagarme\Helper;
use PagarmeCoreApiLib\Controllers\CustomersController;
use PagarmeCoreApiLib\Models\CreateAddressRequest as CreateAddressApiRequest;

/**
 * @method CreateAddressResponse send()
 */
class CreateAddressRequest extends AbstractRequest
{

    /**
     * @inheritDoc
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData()
    {
        $this->validate('address', 'customerReference');

        return $this->getAddress()->getParameters();
    }

    /**
     * @param array $data
     *
     * @return \Omnipay\Pagarme\Message\CreateAddressResponse
     * @throws \PagarmeCoreApiLib\APIException
     */
    public function sendData($data): CreateAddressResponse
    {
        $obCustomer = CustomersController::getInstance();
        $obAddressRequest = new CreateAddressApiRequest();
        Helper::arrayToParams($obAddressRequest, $data);

        /** @var \PagarmeCoreApiLib\Models\GetAddressResponse $obResponse */
        $obResponse = $obCustomer->createAddress($this->getCustomerReference(), $obAddressRequest);

        return new CreateAddressResponse($this, $obResponse->jsonSerialize());
    }
}