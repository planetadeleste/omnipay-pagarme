<?php

namespace Omnipay\Pagarme\Message;

use PagarmeCoreApiLib\Models\GetBillingAddressResponse;
use PagarmeCoreApiLib\Models\GetCustomerResponse;

/**
 * @property string                    $id
 * @property string                    $lastFourDigits
 * @property string                    $brand
 * @property string                    $holderName
 * @property integer                   $expMonth
 * @property integer                   $expYear
 * @property string                    $status
 * @property \DateTime                 $createdAt
 * @property \DateTime                 $updatedAt
 * @property GetBillingAddressResponse $billingAddress
 * @property GetCustomerResponse       $customer
 * @property array                     $metadata
 * @property string                    $type
 * @property string                    $holderDocument
 * @property \DateTime                 $deletedAt
 * @property string                    $firstSixDigits
 * @property string                    $label
 */
class CreateCardResponse extends Response
{

}