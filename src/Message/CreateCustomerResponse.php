<?php

namespace Omnipay\Pagarme\Message;

use PagarmeCoreApiLib\Models\GetAddressResponse;
use PagarmeCoreApiLib\Models\GetPhonesResponse;

/**
 * @property string             $id
 * @property string             $name
 * @property string             $email
 * @property bool               $delinquent
 * @property \DateTime          $createdAt
 * @property \DateTime          $updatedAt
 * @property string             $document
 * @property string             $type
 * @property string             $fbAccessToken
 * @property GetAddressResponse $address
 * @property array              $metadata
 * @property GetPhonesResponse  $phones
 * @property integer            $fbId
 * @property string             $code
 * @property string             $documentType
 */
class CreateCustomerResponse extends Response
{

}