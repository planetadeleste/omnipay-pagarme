<?php

namespace Omnipay\Pagarme\Message;

use PagarmeCoreApiLib\Models\CreateAntifraudRequest;
use PagarmeCoreApiLib\Models\CreateDeviceRequest;
use PagarmeCoreApiLib\Models\CreateLocationRequest;
use PagarmeCoreApiLib\Models\CreateShippingRequest;
use PagarmeCoreApiLib\Models\CreateSubMerchantRequest;

/**
 * @property array                    $items
 * @property CreateCustomerRequest    $customer
 * @property array                    $payments
 * @property string                   $code
 * @property string                   $customerId
 * @property CreateShippingRequest    $shipping
 * @property array                    $metadata
 * @property bool                     $antifraudEnabled
 * @property string                   $ip
 * @property string                   $sessionId
 * @property CreateLocationRequest    $location
 * @property CreateDeviceRequest      $device
 * @property bool                     $closed
 * @property string                   $currency
 * @property CreateAntifraudRequest   $antifraud
 * @property CreateSubMerchantRequest $submerchant
 */
class OrderResponse extends Response
{

}