<?php

namespace Omnipay\Pagarme\Message;

use DateTime;
use PagarmeCoreApiLib\Models\GetCustomerResponse;
use PagarmeCoreApiLib\Models\GetCardResponse;
use PagarmeCoreApiLib\Models\GetDiscountResponse;
use PagarmeCoreApiLib\Models\GetIncrementResponse;
use PagarmeCoreApiLib\Models\GetPeriodResponse;
use PagarmeCoreApiLib\Models\GetSetupResponse;
use PagarmeCoreApiLib\Models\GetSubscriptionItemResponse;
use PagarmeCoreApiLib\Models\GetSubscriptionSplitResponse;

/**
 * @property string                        $code
 * @property DateTime                      $startAt
 * @property string                        $interval
 * @property integer                       $intervalCount
 * @property string                        $billingType
 * @property GetPeriodResponse             $currentCycle
 * @property string                        $paymentMethod
 * @property string                        $currency
 * @property integer                       $installments
 * @property string                        $status
 * @property DateTime                      $createdAt
 * @property DateTime                      $updatedAt
 * @property GetCustomerResponse           $customer
 * @property GetCardResponse               $card
 * @property GetSubscriptionItemResponse[] $items
 * @property string                        $statementDescriptor
 * @property array                         $metadata
 * @property GetSetupResponse              $setup
 * @property string                        $gatewayAffiliationId
 * @property DateTime                      $nextBillingAt
 * @property integer                       $billingDay
 * @property integer                       $minimumPrice
 * @property DateTime                      $canceledAt
 * @property GetDiscountResponse[]         $discounts
 * @property GetIncrementResponse[]        $increments
 * @property integer                       $boletoDueDays
 * @property GetSubscriptionSplitResponse  $split
 */
class SubscribeResponse extends Response
{
    public function getOrderReference(): ?string
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getErrors(): ?array
    {
        if (($arErrors = parent::getErrors()) && !empty($arErrors)) {
            return $arErrors;
        }

        if (!$this->id && !$this->status && $this->message) {
            return array_wrap($this->message);
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->getStatus() === 'active';
    }

    public function isPaid(): bool
    {
        return $this->isActive();
    }

    /**
     * @return bool
     */
    public function isCancelled(): bool
    {
        return !$this->isActive();
    }

    /**
     * @return bool
     */
    public function isFailed(): bool
    {
        return !$this->isActive();
    }

    /**
     * @return bool
     */
    public function isPending(): bool
    {
        return !$this->isActive();
    }
}