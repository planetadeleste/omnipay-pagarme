<?php

namespace Omnipay\Pagarme\Message;

use PagarmeCoreApiLib\Models\GetCustomerResponse;

/**
 * @property string              $id
 * @property string              $street
 * @property string              $number
 * @property string              $complement
 * @property string              $zipCode
 * @property string              $neighborhood
 * @property string              $city
 * @property string              $state
 * @property string              $country
 * @property string              $status
 * @property \DateTime           $createdAt
 * @property \DateTime           $updatedAt
 * @property GetCustomerResponse $customer
 * @property array               $metadata
 * @property string              $line1
 * @property string              $line2
 * @property \DateTime           $deletedAt
 */
class CreateAddressResponse extends Response
{

}