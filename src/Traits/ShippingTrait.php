<?php

namespace Omnipay\Pagarme\Traits;

use Omnipay\Pagarme\Address;

trait ShippingTrait
{
    /**
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setRecipientName($sValue): self
    {
        return $this->setParameter('recipient_name', $sValue);
    }

    /**
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setRecipientPhone($sValue): self
    {
        return $this->setParameter('recipient_phone', $sValue);
    }

    /**
     * @param Address $sValue
     *
     * @return $this
     */
    public function setShippingAddress(Address $sValue): self
    {
        return $this->setParameter('shipping_address', $sValue);
    }

    /**
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setMaxDeliveryDate($sValue): self
    {
        return $this->setParameter('max_delivery_date', $sValue);
    }

    /**
     * @return string|null
     */
    public function getMaxDeliveryDate(): ?string
    {
        return $this->getParameter('max_delivery_date');
    }

    /**
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setEstimatedDeliveryDate($sValue): self
    {
        return $this->setParameter('estimated_delivery_date', $sValue);
    }

    /**
     * @return string|null
     */
    public function getEstimatedDeliveryDate(): ?string
    {
        return $this->getParameter('estimated_delivery_date');
    }

    /**
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setShippingType($sValue): self
    {
        return $this->setParameter('shipping_type', $sValue);
    }

    /**
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setShippingAmount($sValue): self
    {
        return $this->setParameter('shipping_amount', $sValue);
    }

    /**
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setShippingDescription($sValue): self
    {
        return $this->setParameter('shipping_description', $sValue);
    }

    /**
     * @return array
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getShippingData(): array
    {
        $this->validate('recipient_name', 'shipping_type', 'shipping_address');

        $data = [
            'amount'          => (int)$this->getShippingAmount(),
            'description'     => $this->getShippingDescription(),
            'recipient_name'  => $this->getRecipientName(),
            'recipient_phone' => $this->getRecipientPhone(),
            'address'         => $this->getShippingAddress()->getParameters(),
            'type'            => $this->getShippingType()
        ];

        if ($sMaxDeliveryDate = $this->getMaxDeliveryDate()) {
            $data['max_delivery_date'] = $sMaxDeliveryDate;
        }

        if ($sEstimateDeliveryDate = $this->getEstimatedDeliveryDate()) {
            $data['estimated_delivery_date'] = $sEstimateDeliveryDate;
        }

        return $data;
    }

    /**
     * @return string|null
     */
    public function getShippingAmount(): ?string
    {
        return $this->getParameter('shipping_amount');
    }

    /**
     * @return string|null
     */
    public function getShippingDescription(): ?string
    {
        return $this->getParameter('shipping_description');
    }

    /**
     * @return string|null
     */
    public function getRecipientName(): ?string
    {
        return $this->getParameter('recipient_name');
    }

    /**
     * @return string|null
     */
    public function getRecipientPhone(): ?string
    {
        return $this->getParameter('recipient_phone');
    }

    /**
     * @return Address|null
     */
    public function getShippingAddress(): ?Address
    {
        return $this->getParameter('shipping_address');
    }

    /**
     * @return string|null
     */
    public function getShippingType(): ?string
    {
        return $this->getParameter('shipping_type');
    }
}