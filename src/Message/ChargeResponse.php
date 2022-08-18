<?php

namespace Omnipay\Pagarme\Message;

use PagarmeCoreApiLib\Models\GetCustomerResponse;
use PagarmeCoreApiLib\Models\GetInvoiceResponse;
use PagarmeCoreApiLib\Models\GetOrderResponse;
use PagarmeCoreApiLib\Models\GetTransactionResponse;

/**
 * @property string                 $id
 * @property string                 $code
 * @property string                 $gatewayId
 * @property integer                $amount
 * @property string                 $status
 * @property string                 $currency
 * @property string                 $paymentMethod
 * @property \DateTime              $dueAt
 * @property \DateTime              $createdAt
 * @property \DateTime              $updatedAt
 * @property GetTransactionResponse $lastTransaction
 * @property GetInvoiceResponse     $invoice
 * @property GetOrderResponse       $order
 * @property GetCustomerResponse    $customer
 * @property array                  $metadata
 * @property \DateTime              $paidAt
 * @property \DateTime              $canceledAt
 * @property integer                $canceledAmount
 * @property integer                $paidAmountF
 */
class ChargeResponse extends Response
{

}