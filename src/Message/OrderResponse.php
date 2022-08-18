<?php

namespace Omnipay\Pagarme\Message;


use PagarmeCoreApiLib\Models\GetChargeResponse;
use PagarmeCoreApiLib\Models\GetCustomerResponse;
use PagarmeCoreApiLib\Models\GetDeviceResponse;
use PagarmeCoreApiLib\Models\GetLocationResponse;
use PagarmeCoreApiLib\Models\GetShippingResponse;
use PagarmeCoreApiLib\Models\GetTransactionResponse;

/**
 * @property string              $id
 * @property string              $code
 * @property string              $currency
 * @property array               $items
 * @property GetCustomerResponse $customer
 * @property string              $status
 * @property \DateTime           $createdAt
 * @property \DateTime           $updatedAt
 * @property GetChargeResponse[] $charges
 * @property string              $invoiceUrl
 * @property GetShippingResponse $shipping
 * @property array               $metadata
 * @property array               $checkouts
 * @property string              $ip
 * @property string              $sessionId
 * @property GetLocationResponse $location
 * @property GetDeviceResponse   $device
 * @property bool                $closed
 */
class OrderResponse extends Response
{
    const STATUS_PENDING = 'PENDING';
    const STATUS_PAID = 'PAID';
    const STATUS_CANCELED = 'CANCELED';
    const STATUS_FAILED = 'FAILED';

    public function getOrderReference(): ?string
    {
        return $this->id;
    }

    public function getCustomerReference(): ?string
    {
        return $this->customer ? $this->customer->id : null;
    }

    public function getTransactionReference(): ?string
    {
        return ($obTransaction = $this->getLastTransaction()) ? $obTransaction->gatewayId : null;
    }

    public function getLastTransaction(): ?GetTransactionResponse
    {
        $arCharges = $this->charges;

        if (empty($arCharges)) {
            return null;
        }


        if ($obCharge = $arCharges[0]) {
            return $obCharge->lastTransaction;
        }

        return null;
    }

    public function getErrors(): ?array
    {
        if (($arErrors = parent::getErrors()) && !empty($arErrors)) {
            return $arErrors;
        }

        if (($obGatewayResponse = $this->getGatewayResponse()) && !empty($obGatewayResponse->errors)) {
            $obError = $obGatewayResponse->errors[0];
            return [
                'gateway.response' => [$obError->message]
            ];
        }

        return null;
    }

    /**
     * @return false|mixed|\PagarmeCoreApiLib\Models\GetGatewayResponseResponse
     */
    public function getGatewayResponse()
    {
        return ($obTransaction = $this->getLastTransaction()) ? $obTransaction->gatewayResponse : null;
    }

    public function getGatewayStatus(): ?string
    {
        if (!$obTransaction = $this->getLastTransaction()) {
            return null;
        }

        return $obTransaction->status;
    }

    public function getTransactionId(): ?string
    {
        return ($obTransaction = $this->getLastTransaction()) ? $obTransaction->id : null;
    }

    public function getPaymentToken(): ?string
    {
        return ($obTransaction = $this->getLastTransaction()) ? $obTransaction->gatewayId : null;
    }

    /**
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->getStatus() === self::STATUS_PAID;
    }

    public function getStatus(): string
    {
        $sStatus = $this->status;
        if (!empty($this->charges)) {
            $arCharges = is_array($this->charges) ? $this->charges : [$this->charges];
            foreach ($arCharges as $obCharge) {
                if ($obCharge->status !== $sStatus) {
                    $sStatus = $obCharge->status;
                }
            }
        }

        return strtoupper($sStatus);
    }

    /**
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->getStatus() === self::STATUS_CANCELED;
    }

    /**
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->getStatus() === self::STATUS_FAILED;
    }

    /**
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->getStatus() === self::STATUS_PENDING;
    }

}