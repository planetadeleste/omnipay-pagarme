<?php

namespace Omnipay\Pagarme\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Pagarme\Helper;
use PagarmeCoreApiLib\APIException;
use PagarmeCoreApiLib\Controllers\OrdersController;
use PagarmeCoreApiLib\Controllers\SubscriptionsController;
use PagarmeCoreApiLib\Models\CreateDeviceRequest;
use PagarmeCoreApiLib\Models\CreateOrderItemRequest;
use PagarmeCoreApiLib\Models\CreateOrderRequest;
use PagarmeCoreApiLib\Models\CreateSubscriptionItemRequest;
use PagarmeCoreApiLib\Models\CreateSubscriptionRequest;

/**
 * @link https://docs.pagar.me/reference/criar-assinatura-avulsa
 */
class SubscribeRequest extends AuthorizeRequest
{

    /**
     * @inheritDoc
     */
    public function getData()
    {
        $this->validate('paymentMethod');

        $sPaymentMethod = strtolower($this->getPaymentMethod());

        $data = [
            'code'                 => $this->getCode(),
            'payment_method'       => $sPaymentMethod,
            'currency'             => 'BRL',
            'start_at'             => $this->getStartAt(),
            'interval'             => $this->getInterval(),
            'interval_count'       => $this->getIntervalCount(),
            'billing_type'         => $this->getBillingType(),
            'installments'         => $this->getInstallments(),
            'statement_descriptor' => $this->getStatementDescriptor(),
            'boleto_due_days'      => $this->getBoletoDueDays(),
        ];

        // Customer
        if ($iCustomerId = $this->getCustomerReference()) {
            $data['customer_id'] = $iCustomerId;
        } else {
            $data['customer'] = $this->getCustomer();
        }

        // Payment
        if ($sPaymentMethod === 'credit_card') {
            if ($sCardId = $this->getCardId()) {
                $data['card_id'] = $sCardId;
            } elseif ($sCartToken = $this->getCardToken()) {
                $data['card_token'] = $sCartToken;
            } else {
                $data['card'] = $this->getCardData();
                if (!array_get($data, 'card.holder_name') && array_get($data, 'customer')) {
                    array_set($data, 'card.holder_name', array_get($data, 'customer.name'));
                }
            }
        }

        // Add Items
        if (($arItems = $this->getItems()) && $arItems->count()) {
            $arItemsData = array_filter($arItems->getData(), function ($arItem) {
                return array_get($arItem, 'code') !== 'addition';
            });
            if (!empty($arItemsData)) {
                $arItemsData = array_map(static function (array $arItem) {
                    $arItem['pricing_scheme'] = [
                        'scheme_type'   => 'unit',
                        'price'         => $arItem['amount'],
                        'mininum_price' => $arItem['amount'],
                    ];
                    unset($arItem['amount']);

                    return $arItem;
                }, $arItemsData);
                $data['items'] = $arItemsData;
            }

            // Increments
            $arAdditionItemsData = array_filter($arItems->getData(), function ($arItem) {
                return array_get($arItem, 'code') === 'addition';
            });
            if (!empty($arAdditionItemsData)) {
                $arIncrementsData = array_map(function (array $arItem) {
                    return [
                        'value'          => $arItem['amount'],
                        'increment_type' => 'flat',
                        'cycles'         => 1
                    ];
                }, $arAdditionItemsData);
                array_set($data, 'inscrements', $arIncrementsData);
            }
        }

        return $data;
    }

    /**
     * @param $data
     *
     * @return \Omnipay\Pagarme\Message\SubscribeResponse
     */
    public function sendData($data): Response
    {
        try {
            $obController = SubscriptionsController::getInstance();

            // Set Items
            $arItems = [];
            foreach ($data['items'] as $arItem) {
                $obItem = new CreateSubscriptionItemRequest();
                Helper::arrayToParams($obItem, $arItem);
                $arItems[] = $obItem;
            }
            Helper::arraySet($data, 'items', $arItems);

            /** @var CreateSubscriptionRequest $obSubscriptionRequest */
            /** @var \PagarmeCoreApiLib\Models\GetSubscriptionResponse $obResponse */

            $obSubscriptionRequest = Helper::arrayToParams(new CreateSubscriptionRequest(), $data);
            $obResponse = $obController->createSubscription($obSubscriptionRequest);

            return new SubscribeResponse($this, $obResponse->jsonSerialize());
        } catch (APIException $ex) {
            $sResponseBody = $ex->getContext()->getResponse()->getRawBody();
            $arResponse = json_decode($sResponseBody, true);

            return new SubscribeResponse($this, $arResponse);
        }
    }

    /**
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setStartAt($sValue): self
    {
        return $this->setParameter('start_at', $sValue);
    }

    /**
     * Data de início da assinatura.
     * Se não for informada, a assinatura será iniciada imediatamente.
     *
     * @return string|null
     */
    public function getStartAt(): ?string
    {
        return $this->getParameter('start_at');
    }

    /**
     * Intervalo da recorrência.
     * Valores possíveis: day, week, month ou year.
     *
     * @param mixed $sValue
     *
     * @return $this
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function setInterval($sValue): self
    {
        $sValue = strtolower($sValue);

        if (!in_array($sValue, ['day', 'week', 'month', 'year'])) {
            throw new InvalidRequestException("Invalid interval. Accepted only 'day', 'week', 'month', 'year'");
        }

        return $this->setParameter('interval', $sValue);
    }

    /**
     * @return string|null
     */
    public function getInterval(): string
    {
        return $this->getParameter('interval', 'month');
    }

    /**
     * Valor mínimo em centavos da assinatura.
     *
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setMinimumPrice($sValue): self
    {
        return $this->setParameter('minimum_price', $sValue);
    }

    /**
     * @return string|null
     */
    public function getMinimumPrice(): ?string
    {
        return $this->getParameter('minimum_price');
    }

    /**
     * Número de intervalos de acordo com a propriedade interval entre cada cobrança da assinatura.
     * Ex.: plano mensal = interval_count (1) e interval (month)
     * plano trimestral = interval_count (3) e interval (month)
     * plano semestral = interval_count (6) e interval (month)
     *
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setIntervalCount($sValue): self
    {
        return $this->setParameter('interval_count', $sValue);
    }

    /**
     * @return int
     */
    public function getIntervalCount(): int
    {
        return (int)$this->getParameter('interval_count', 1);
    }

    /**
     * Tipo de cobrança.
     * Valores possíveis: prepaid, postpaid ou exact_day.
     *
     * @param mixed $sValue
     *
     * @return $this
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function setBillingType($sValue): self
    {
        $sValue = strtolower($sValue);

        if (!in_array($sValue, ['prepaid', 'postpaid', 'exact_day'])) {
            throw new InvalidRequestException("Invalid interval. Accepted only 'prepaid', 'postpaid', 'exact_day'");
        }

        return $this->setParameter('billing_type', $sValue);
    }

    /**
     * @return string|null
     */
    public function getBillingType(): string
    {
        return $this->getParameter('billing_type', 'prepaid');
    }

    /**
     * Dia da cobrança.
     * Obrigatório, caso o billing_type seja exact_day
     *
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setBillingDay($sValue): self
    {
        return $this->setParameter('billing_day', $sValue);
    }

    /**
     * @return string|null
     */
    public function getBillingDay(): ?string
    {
        return $this->getParameter('billing_day');
    }

    /**
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setGatewayAffiliationId($sValue): self
    {
        return $this->setParameter('gateway_affiliation_id', $sValue);
    }

    /**
     * @return string|null
     */
    public function getGatewayAffiliationId(): ?string
    {
        return $this->getParameter('gateway_affiliation_id');
    }

    /**
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setBoletoDueDays($sValue): self
    {
        return $this->setParameter('boleto_due_days', $sValue);
    }

    /**
     * @return string|null
     */
    public function getBoletoDueDays(): ?string
    {
        return $this->getParameter('boleto_due_days');
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint.'subscriptions';
    }
}